<?php

class TM_Core_Model_Resource_Module_RemoteCollection extends Varien_Data_Collection
{
    const XML_FEED_URL_PATH = 'tmcore/modules/feed_url';

    protected $_collectedModules = array();

    /**
     * Lauch data collecting
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Varien_Data_Collection_Filesystem
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        try {
            $client  = new Zend_Http_Client();
            $adapter = new Zend_Http_Client_Adapter_Curl();
            $client->setAdapter($adapter);
            $client->setUri($this->_getFeedUri());
            $client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
            $client->setParameterGet('domain', Mage::app()->getRequest()->getHttpHost());
            $responseBody = $client->request()->getBody();
            $modules      = Mage::helper('core')->jsonDecode($responseBody);
            if (!is_array($modules)) {
                throw new Exception('Decoding failed');
            }
        } catch (Exception $e) {
            // @todo remove this fix and add error message
            $modules = array(
                'TM_Core' => array(
                    'code'          => 'TM_Core',
                    'version'       => '',
                    'changelog'     => '',
                    'link'          => '',
                    'download_link' => '',
                    'identity_key_link' => ''
                ),
                'TM_License' => array(
                    'code'          => 'TM_License',
                    'version'       => '',
                    'changelog'     => '',
                    'link'          => '',
                    'download_link' => '',
                    'identity_key_link' => ''
                ),
                'TM_Argento' => array(
                    'code'          => 'TM_Argento',
                    'version'       => '',
                    'changelog'     => '',
                    'link'          => '',
                    'download_link' => '',
                    'identity_key_link' => '',
                    'changelog'     => ""
                ),
                'TM_ArgentoArgento' => array(
                    'code'          => 'TM_ArgentoArgento',
                    'version'       => '',
                    'changelog'     => "",
                    'link'          => 'http://argentotheme.com',
                    'download_link' => 'https://argentotheme.com/downloadable/customer/products/',
                    'identity_key_link' => 'https://argentotheme.com/license/customer/identity/'
                ),
                'TM_ArgentoMage2Cloud' => array(
                    'code'          => 'TM_ArgentoMage2Cloud',
                    'version'       => '',
                    'changelog'     => "",
                    'link'          => 'http://argentotheme.com',
                    'download_link' => '',
                    'identity_key_link' => ''
                ),
                'TM_ArgentoMall' => array(
                    'code'          => 'TM_ArgentoMall',
                    'version'       => '',
                    'changelog'     => "",
                    'link'          => 'http://argentotheme.com',
                    'download_link' => 'https://argentotheme.com/downloadable/customer/products/',
                    'identity_key_link' => 'https://argentotheme.com/license/customer/identity/'
                ),
                'TM_ArgentoPure' => array(
                    'code'          => 'TM_ArgentoPure',
                    'version'       => '',
                    'changelog'     => "",
                    'link'          => 'http://argentotheme.com',
                    'download_link' => 'https://argentotheme.com/downloadable/customer/products/',
                    'identity_key_link' => 'https://argentotheme.com/license/customer/identity/'
                )
            );
        }

        foreach ($modules as $moduleName => $values) {
            $values['id'] = $values['code'];
            $this->_collectedModules[$values['code']] = $values;
        }

        // calculate totals
        $this->_totalRecords = count($this->_collectedModules);
        $this->_setIsLoaded();

        // paginate and add items
        $from = ($this->getCurPage() - 1) * $this->getPageSize();
        $to = $from + $this->getPageSize() - 1;
        $isPaginated = $this->getPageSize() > 0;

        $cnt = 0;
        foreach ($this->_collectedModules as $row) {
            $cnt++;
            if ($isPaginated && ($cnt < $from || $cnt > $to)) {
                continue;
            }
            $item = new $this->_itemObjectClass();
            $this->addItem($item->addData($row));
            if (!$item->hasId()) {
                $item->setId($cnt);
            }
        }

        return $this;
    }

    protected function _getFeedUri()
    {
        $useHttps = Mage::getStoreConfigFlag(TM_Core_Model_Module::XML_USE_HTTPS_PATH);
        return ($useHttps ? 'https://' : 'http://')
            . Mage::getStoreConfig(self::XML_FEED_URL_PATH);
    }
}
