<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */


class Amasty_Audit_Block_Adminhtml_Visit_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setTitle(Mage::helper('amaudit')->__('Page Visit History'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('data', array(
            'label' => Mage::helper('amaudit')->__('Admin Data'),
            'content' => $this->getLayout()->createBlock('amaudit/adminhtml_visit_edit_tab_data')->toHtml(),
            'active' => true
        ));

        $this->addTab('history', array(
            'label' => Mage::helper('amaudit')->__('Visits History'),
            'content' => $this->getLayout()->createBlock('amaudit/adminhtml_visit_edit_tab_history')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}
