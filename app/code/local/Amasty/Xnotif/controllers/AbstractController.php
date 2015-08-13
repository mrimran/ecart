<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */   
class Amasty_Xnotif_AbstractController extends Mage_Core_Controller_Front_Action
{
    protected $_title;
    protected $_type;
    
    public function preDispatch()
    {
        parent::preDispatch();

        $loginUrl = Mage::helper('customer')->getLoginUrl();
        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }
    
    public function indexAction() 
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('head')->setTitle($this->_title);
        $this->renderLayout();
    }
    
    public function removeAction()
    {
        $id = (int) $this->getRequest()->getParam('item');
        
        $modelName =  'productalert/' . $this->_type;
        $item = Mage::getModel($modelName)->load($id);
        $_customer = Mage::getModel('customer/customer')
                        ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                        ->load(Mage::getSingleton('customer/session')->getCustomer()->getId());
                        
         // check if not a guest subscription (cust. id is set) and is matching with logged in customer
        if ( $item->getCustomerId() > 0 && $item->getCustomerId() == $_customer->getId() ){
            try {
                $item->delete();
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')->addError(
                    $this->__('An error occurred while deleting the item from Subscriptions: %s', $e->getMessage())
                );
            }
            catch(Exception $e) {
                Mage::getSingleton('customer/session')->addError(
                    $this->__('An error occurred while deleting the item from Subscriptions.')
                );
            }
        }
        $this->_redirectReferer(Mage::getUrl('*/*'));
    }
} 