<?php

class MW_Mcore_Block_Adminhtml_System_Config_Extension extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {    	    
		$html =  $this->getLayout()->createBlock('mcore/adminhtml_system_config_extensioninfo')->setTemplate('mw_mcore/extensions.phtml')->toHtml();
        return $html;
    }
}