<?php
class MW_FollowUpEmail_Block_Adminhtml_Rules extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_rules';
    $this->_blockGroup = 'followupemail';
    $this->_headerText = $this->__('Manage Rules');
    $this->_addButtonLabel = Mage::helper('followupemail')->__('Add New Rule');
    parent::__construct();
  }
}