<?php

class TM_SuggestPage_Model_Observer
{
    public function addToCartComplete(Varien_Event_Observer $observer)
    {
        if ($observer->getRequest()->getParam('return_url') // paypal express button fix
            || !Mage::getStoreConfig('suggestpage/general/show_after_addtocart')) {

            return;
        }
        if ($this->_isMobile() && !Mage::getStoreConfig('suggestpage/mobile/show_after_addtocart')) {
            return;
        }

        $observer->getResponse()->setRedirect(Mage::getUrl('suggest'));
        $session = Mage::getSingleton('checkout/session');
        $session->setNoCartRedirect(true);
        $session->setSuggestpageProductId($observer->getProduct()->getId());
        $session->setViewCartUrl(Mage::getUrl('suggest')); //ajaxpro integration

        $message = Mage::helper('checkout')->__(
            '%s was added to your shopping cart.',
            Mage::helper('core')->htmlEscape($observer->getProduct()->getName())
        );
        $session->addSuccess($message);
    }

    // https://github.com/mrlynn/MobileBrowserDetectionExample
    private function _isMobile()
    {
        $isMobile = false;
        if(isset($_SERVER['HTTP_USER_AGENT'])
            && preg_match('/(android|up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {

            $isMobile = true;
        }
        if((isset($_SERVER['HTTP_ACCEPT']) && (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0))
            || ((isset($_SERVER['HTTP_X_WAP_PROFILE'])
            || isset($_SERVER['HTTP_PROFILE'])))) {

            $isMobile = true;
        }

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $mobileUserAgent = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
            $mobileAgents = array(
                'w3c ','acs-','alav','alca','amoi','andr','audi','avan','benq',
                'bird','blac','blaz','brew','cell','cldc','cmd-','dang','doco',
                'eric','hipt','inno','ipaq','java','jigs','kddi','keji','leno',
                'lg-c','lg-d','lg-g','lge-','maui','maxo','midp','mits','mmef',
                'mobi','mot-','moto','mwbp','nec-','newt','noki','oper','palm',
                'pana','pant','phil','play','port','prox','qwap','sage','sams',
                'sany','sch-','sec-','send','seri','sgh-','shar','sie-','siem',
                'smal','smar','sony','sph-','symb','t-mo','teli','tim-','tosh',
                'tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
                'wapr','webc','winw','winw','xda','xda-'
            );
            if(in_array($mobileUserAgent, $mobileAgents)) {
                $isMobile = true;
            }
        }

        if (isset($_SERVER['ALL_HTTP'])) {
            if (strpos(strtolower($_SERVER['ALL_HTTP']), 'OperaMini') > 0) {
                $isMobile = true;
            }
        }
        if (isset($_SERVER['HTTP_USER_AGENT'])
            && strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') > 0) {

            $isMobile = false;
        }
        return $isMobile;
    }

    public function addAjaxProMessageHandleOption(Varien_Event_Observer $observer)
    {
        $object  = $observer->getObject();
        $options = $object->getOptions();
        $options[] = array(
            'value' => 'tm_ajaxpro_checkout_cart_add_suggestpage',
            'label' => Mage::helper('suggestpage')->__('SuggestPage Content')
        );
        $object->setOptions($options);
    }

    public function prepareLayoutHandlesAndBlocks(Varien_Event_Observer $observer)
    {
        $object     = $observer->getObject();
        $handles    = $object->getHandles();
        $blockNames = $object->getBlockNames();
        $request    = $observer->getObserver()->getControllerAction()->getRequest();

        $suggestpageHandles = array(
            'suggestpage_index_index',
            'tm_ajaxpro_checkout_cart_add_suggestpage'
        );
        if (!array_intersect($suggestpageHandles, $handles)) {
            return;
        }

        if ('delete' == $request->getActionName()) {
            $handles = $this->_replaceArrayValues($handles, array(
                'tm_ajaxpro_checkout_cart_add_suggestpage'
                    => 'tm_ajaxpro_checkout_cart_add_with_cart_extended'
            ));
        } else {
            // hide all popups, when on the suggest page
            if (in_array('suggestpage_index_index', $handles)) {
                $blockNames[] = 'content';
                $blockNames[] = 'catalog_product_price_template';
                $blockNames = $this->_replaceArrayValues($blockNames, array(
                    'ajaxpro_message' => false
                ));
                // need to remove all popup handles to prevent their appearence inside of content block
                $handles = $this->_replaceArrayValues($handles, array(
                    'tm_ajaxpro_checkout_cart_add_*' => false,
                    'suggestpage_view' => false // duplicate ajaxpro layout handle bugfix
                ));
            } elseif (in_array('tm_ajaxpro_checkout_cart_add_suggestpage', $handles)) {
                // modify handles, when suggestpage should be shown in popup
                $handles = array('default', 'tm_ajaxpro_checkout_cart_add_suggestpage');
                $blockNames[] = 'content';
                $blockNames[] = 'catalog_product_price_template';
            }
        }

        $object->setBlockNames($blockNames);
        $object->setHandles($handles);
    }

    protected function _replaceArrayValues($array, $rules)
    {
        foreach ($rules as $search => $replace) {
            if (strstr($search, '*')) {
                $index = false;
                $search = str_replace('*', '', $search);
                foreach ($array as $i => $str) {
                    if (false !== strpos($str, $search)) {
                        $array = $this->_replaceArrayValues($array, array(
                            $str => $replace
                        ));
                    }
                }
            } else {
                $index = array_search($search, $array);
            }
            if (false === $index) {
                continue;
            }
            if (!$replace) {
                unset($array[$index]);
            } else {
                $array[$index] = $replace;
            }
        }
        return array_unique($array);
    }
}
