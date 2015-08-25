<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_Block_Adminhtml_Userlog_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
   
    public function __construct()
    {
        parent::__construct();
        $this->setTitle(Mage::helper('amaudit')->__('Action Log Details'));
    }

        protected function _beforeToHtml()
    {
        $this->addTab('view', array(
            'label'     => Mage::helper('amaudit')->__('Item Information'),
            'content'   => $this->getLayout()->createBlock('amaudit/adminhtml_userlog_edit_tab_view')->toHtml(),
            'active'    => true
        ));

        return parent::_beforeToHtml();
    }
}
