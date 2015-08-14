<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Geoip
 */
class Amasty_Geoip_Model_Observer
{
    public function onSystemConfiguration($observer)
    {
        $section = Mage::app()->getRequest()->getParam('section');
        if ($section == 'amgeoip') {
            Mage::getSingleton('adminhtml/session')->addWarning('When import in progress please do not close this browser window and do not attempt to operate Magento backend in separate tabs. Import usually takes from 10 to 20 minutes.');
        }
    }
}