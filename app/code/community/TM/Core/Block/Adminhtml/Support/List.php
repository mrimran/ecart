<?php

class TM_Core_Block_Adminhtml_Support_List extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_support_list';
        $this->_blockGroup = 'tmcore';
        $this->_headerText = Mage::helper('tmcore')->__('Reports');
        parent::__construct();
    }
}
