<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_Adminhtml_ActiveController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/amaudit');
        $this->_title($this->__('Active Sessions'));

        $this->_addBreadcrumb($this->__('Admin Actions Log'), $this->__('Active Sessions'));
        $block = $this->getLayout()->createBlock('amaudit/adminhtml_active');
        $this->_addContent($block);
        $this->renderLayout();
    }

    public function terminateAction()
    {
        $sessionId = $this->getRequest()->getParam('session_id');
        $activeModel= Mage::getModel('amaudit/active');
        $activeModel->removeOnlineAdmin($sessionId);
        $activeModel->destroySession($sessionId);
        $this->_redirect('*/*/index');
    }

}