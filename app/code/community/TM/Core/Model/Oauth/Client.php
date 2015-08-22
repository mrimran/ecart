<?php
/**
 * http://inchoo.net/ecommerce/magento/consuming-magento-rest-zend_oauth_consumer/
 * http://www.magentocommerce.com/knowledge-base/entry/how-to-use-extend-the-magento-rest-api-to-use-coupon-auto-generation
 *
 * @author     Darko Goleš <darko.goles@inchoo.net>
 * @author     Alexander Krasko <0m3r.mail@gmail.com>
 * @package    Inchoo
 * @subpackage RestConnect
 */
class TM_Core_Model_Oauth_Client extends Mage_Core_Model_Abstract
{
    private $_callbackUrl;
    private $_siteUrl;
    private $_consumerKey;
    private $_consumerSecret;
    private $_requestTokenUrl;
    private $_accessTokenUrl;
    private $_consumer;
    private $_authorizeUrl;
    private $_userAuthorizationUrl;
    private $_authorized_token;

    const OAUTH_STATE_NO_TOKEN      = 0;
    const OAUTH_STATE_REQUEST_TOKEN = 1;
    const OAUTH_STATE_ACCESS_TOKEN  = 2;
    const OAUTH_STATE_ERROR         = 3;

    public function init($config)
    {
        $this->setOAuthConfig($config);
        return $this;
    }

    public function setAuthorizedToken($token)
    {
        $this->_authorized_token = $token;
    }

    public function getAuthorizedToken()
    {
        if ($this->_authorized_token) {
            return $this->_authorized_token;
        }
        return false;
    }

    public function reset()
    {
        return $this->resetSessionParams();
    }

    public function authenticate()
    {
        $state = $this->getOAuthState();
        $consumer = $this->_getOAuthConsumer();
        try {
            switch ($state) {
                case self::OAUTH_STATE_NO_TOKEN:
                    $requestToken = $this->getRequestToken();
                    $this->setOAuthState(self::OAUTH_STATE_REQUEST_TOKEN);
                    $consumer->redirect();
                    return self::OAUTH_STATE_REQUEST_TOKEN;
                    break;
                case self::OAUTH_STATE_REQUEST_TOKEN:
                    $accessToken = $this->getAccessToken($this->getRequestToken());
                    $this->setAuthorizedToken($accessToken);
                    $this->setOAuthState(self::OAUTH_STATE_ACCESS_TOKEN);
                    return self::OAUTH_STATE_ACCESS_TOKEN;
                    break;
                case self::OAUTH_STATE_ACCESS_TOKEN:
                    $accessToken = $this->_getAccessTokenFromSession();
                    if ($accessToken && $accessToken instanceof Zend_Oauth_Token_Access) {
                        $this->setAuthorizedToken($accessToken);
                    }
                    return self::OAUTH_STATE_ACCESS_TOKEN;
                default:
                    $this->resetSessionParams();
                    return self::OAUTH_STATE_NO_TOKEN;
                    return;
                    break;
            }
        } catch (Zend_Oauth_Exception $e) {
            $this->resetSessionParams();
            Mage::logException($e);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return self::OAUTH_STATE_NO_TOKEN;
    }

    private function resetSessionParams()
    {
        $this->getSession()->unsetData('o_auth_state');
        $this->getSession()->unsetData('request_token');
        $this->getSession()->unsetData('o_auth_config');
        $this->getSession()->unsetData('access_token');
        return $this;
    }

    public function getRequestToken()
    {
        $token = $this->_getRequestTokenFromSession();
        if ($token && $token instanceof Zend_Oauth_Token_Request) {
            return $token;
        }
        $token = $this->_getRequestTokenFromServer();
        if ($token && $token instanceof Zend_Oauth_Token_Request) {
            $this->_saveRequestTokenInSession($token);
            return $token;
        }
        return false;
    }

    public function getAccessToken($requestToken)
    {
        $token = $this->_getAccessTokenFromSession();
        if ($token && $token instanceof Zend_Oauth_Token_Access) {
            return $token;
        }
        $token = $this->_getAcessTokenFromServer($requestToken);
        if ($token && $token instanceof Zend_Oauth_Token_Access) {
            $this->_saveAccessTokenInSesion($token);
            return $token;
        }
    }

    private function _getAcessTokenFromServer($requestToken)
    {
        if ($requestToken && $requestToken instanceof Zend_Oauth_Token_Request) {
            $accToken = $this->_getOAuthConsumer()->getAccessToken(
                $_GET,
                $requestToken
            );
        }
        if ($accToken && $accToken instanceof Zend_Oauth_Token_Access) {
            return $accToken;
        }
        return false;
    }

    private function _saveAccessTokenInSesion($accessToken)
    {
        $this->getSession()->setAccessToken(serialize($accessToken));
    }

    private function _getAccessTokenFromSession()
    {
        $accessToken = unserialize($this->getSession()->getData('access_token'));
        if ($accessToken && $accessToken instanceof Zend_Oauth_Token_Access) {
            return $accessToken;
        }
        return false;
    }

    private function _getRequestTokenFromServer()
    {
        $token = $this->_getOAuthConsumer()->getRequestToken();
        return $token;
    }

    private function _saveRequestTokenInSession(Zend_Oauth_Token_Request $requestToken)
    {
        $this->getSession()->setRequestToken(serialize($requestToken));
    }

    private function _getRequestTokenFromSession()
    {
        $requestToken = unserialize($this->getSession()->getRequestToken());
        if ($requestToken && $requestToken instanceof Zend_Oauth_Token_Request) {
            return $requestToken;
        }
        return false;
    }

    public function getSession()
    {
        return Mage::getSingleton('core/session');
    }

    public function getOAuthToken()
    {
        return $this->getRequest()->getParam('oauth_token', false);
    }

    public function getRequest()
    {
        return Mage::app()->getRequest();
    }

    private function _getOAuthConsumer()
    {
        if ($consumer = $this->_consumer) {
            if ($consumer instanceof Zend_Oauth_Consumer) {
                return $this->_consumer;
            }
        }
        $this->_consumer = new Zend_Oauth_Consumer($this->getOAuthConfig());
        return $this->_consumer;
    }

    private function getOAuthConfig()
    {
        $config = array(
            'callbackUrl'     => $this->_callbackUrl,
            'siteUrl'         => $this->_siteUrl,
            'consumerKey'     => $this->_consumerKey,
            'consumerSecret'  => $this->_consumerSecret,
            'requestTokenUrl' => $this->_requestTokenUrl,
            'accessTokenUrl'  => $this->_accessTokenUrl,
        );
        if ($this->_authorizeUrl && $this->_authorizeUrl != '') {
            $config['authorizeUrl'] = $this->_authorizeUrl;
        }
        if ($this->_userAuthorizationUrl && $this->_userAuthorizationUrl != '') {
            $config['userAuthorizationUrl'] = $this->_userAuthorizationUrl;
        }
        return $config;
    }

    private function setOAuthConfig($config)
    {
        $this->getSession()->setOAuthConfig(serialize($config));
        foreach ($config as $key => $val) {
            $_key = '_' . $key;
            $this->$_key = $val;
        }
    }

    public function getConfigFromSession()
    {
        $config = unserialize($this->getSession()->getOAuthConfig());
        if ($config && is_array($config)) {
            return $config;
        }
        return false;
    }

    private function setOAuthState($state)
    {
        $this->getSession()->setOAuthState($state);
    }

    public function getOAuthState()
    {
        $state = $this->getSession()->getOAuthState();
        if ($state == null) {
            return self::OAUTH_STATE_NO_TOKEN;
        }
        $paramOAuthToken = $this->getOAuthToken();
        if ($paramOAuthToken == false && $state == self::OAUTH_STATE_REQUEST_TOKEN) {
            $this->resetSessionParams();
            return self::OAUTH_STATE_NO_TOKEN;
        }
        return $state;
    }
}