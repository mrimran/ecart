<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */


class Amasty_Audit_Adminhtml_VisitController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/amaudit');
        $this->_title($this->__('Page Visit History'));

        $this->_addBreadcrumb($this->__('Admin Actions Log'), $this->__('Page Visit History'));
        $block = $this->getLayout()->createBlock('amaudit/adminhtml_visit');
        $this->_addContent($block);
        $this->renderLayout();
    }

    public function clearAction()
    {
        $tableVisit = Mage::getSingleton('core/resource')->getTableName('amaudit/visit');

        $sessionId = session_id();

        Mage::getSingleton('core/resource')
            ->getConnection('core_write')
            ->query("DELETE FROM `$tableVisit` WHERE session_id <> '$sessionId'")
        ;

        $tableVisitDetails = Mage::getSingleton('core/resource')->getTableName('amaudit/visit_detail');

        Mage::getSingleton('core/resource')
            ->getConnection('core_write')
            ->query("DELETE FROM `$tableVisitDetails` WHERE session_id <> '$sessionId'")
        ;

        $this->_redirect('amaudit/adminhtml_visit/index');
    }

    public function editAction()
    {
        $this->loadLayout();
        $entityId = (int)$this->getRequest()->getParam('id');
        $logEntity = Mage::getModel('amaudit/visit')->load($entityId);

        $this->_title($this->__('Details'))
            ->_title($this->__('Page Visit History')
            );

        if (!is_null(Mage::registry('current_session_history'))) {
            Mage::unregister('current_session_history');
        }
        Mage::register('current_session_history', $logEntity);

        $this->_setActiveMenu('system/amaudit');
        $this->renderLayout();
    }
}