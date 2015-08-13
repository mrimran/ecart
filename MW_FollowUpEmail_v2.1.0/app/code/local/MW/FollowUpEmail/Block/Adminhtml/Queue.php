<?php
class MW_FollowUpEmail_Block_Adminhtml_Queue extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_queue';
    $this->_blockGroup = 'followupemail';
    $this->_headerText = Mage::helper('followupemail')->__('Follow Up Email Queue');
    //$this->_addButtonLabel = Mage::helper('followupemail')->__('Add Item');
    parent::__construct();
	$this->_removeButton('add');
  }
}