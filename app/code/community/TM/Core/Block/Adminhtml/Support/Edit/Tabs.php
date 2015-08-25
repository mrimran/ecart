<?php

class TM_Core_Block_Adminhtml_Support_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('support_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('tmcore')->__('Support Ticket'));
    }
}
