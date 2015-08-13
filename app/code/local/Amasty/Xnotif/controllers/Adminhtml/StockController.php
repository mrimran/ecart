<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */
class Amasty_Xnotif_Adminhtml_StockController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction() 
	{
	    $this->loadLayout(); 
        $this->_setActiveMenu('report/amxnotif_stock');
        if (!Mage::helper('ambase')->isVersionLessThan(1,4)){
            $this
                ->_title($this->__('Reports'))
                ->_title($this->__('Alerts'))
                ->_title($this->__('Stock Alerts')); 
        }       
        $this->_addBreadcrumb($this->__('Alerts'), $this->__('Stock Alerts')); 
        $this->_addContent($this->getLayout()->createBlock('amxnotif/adminhtml_stock')); 	    
 	    $this->renderLayout();
	}

    public function deleteAction()
	{
        $alertId = (int) $this->getRequest()->getParam('alert_stock_id');

        if(!$alertId){
            Mage::getSingleton('adminhtml/session')->addError(
                $this->__('An error occurred while deleting the item from Subscriptions.')
            );
        }
        else{
            $alert = Mage::getModel('productalert/stock')->load($alertId);
            if ( $alert && $alert->getId() ){
                try {
                    $alert->delete();
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->__('The item has been deleted from Subscriptions.')
                    );
                }
                catch(Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError(
                        $this->__('An error occurred while deleting the item from Subscriptions.')
                    );
                }
            }
        }

        $this->_redirectReferer();
	}

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/amxnotif_stock');
    }
}