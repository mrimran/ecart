<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */

class Amasty_Audit_Block_Adminhtml_Settings_Geolocation extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;

        $element->setDisabled(true);

        if(isset($modulesArray['Amasty_Geoip'])) {
            $amGeoIpModel = Mage::getModel('amgeoip/import');
            $element->setDisabled(!$amGeoIpModel->isDone());
        }

        return parent::_getElementHtml($element);
    }
}
