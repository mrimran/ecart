<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_AjaxController extends Mage_Adminhtml_Controller_Action
{
   
    public function ajaxAction() 
    {
        $idItem = Mage::app()->getRequest()->getParam('idItem');
        Mage::register('current_log', Mage::getModel('amaudit/log')->load($idItem));
        $block = $this->getLayout()->createBlock('amaudit/adminhtml_userlog_edit_tab_view_details');
        $this->getResponse()->setBody($block->toHtml());
    }
}