<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
class Amasty_Xnotif_Adminhtml_PriceController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction() 
    {
        $this->loadLayout(); 
        $this->_setActiveMenu('report/amxnotif_price');
        if (!Mage::helper('ambase')->isVersionLessThan(1,4)){
            $this
                ->_title($this->__('Reports'))
                ->_title($this->__('Alerts'))
                ->_title($this->__('Stock Alerts')); 
        }       
        $this->_addBreadcrumb($this->__('Alerts'), $this->__('Price Alerts')); 
        $this->_addContent($this->getLayout()->createBlock('amxnotif/adminhtml_price'));         
         $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/amxnotif_price');
    }
}