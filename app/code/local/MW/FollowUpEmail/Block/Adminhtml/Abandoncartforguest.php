<?php
class MW_FollowUpEmail_Block_Adminhtml_Abandoncartforguest extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_abandoncartforguest';
    $this->_blockGroup = 'followupemail';
    $this->_headerText = $this->__('Abandoned Carts for guest');
	
    //$this->_addButtonLabel = Mage::helper('followupemail')->__('Add New Rule');
    parent::__construct();
	$this->_removeButton('add');
  }
}