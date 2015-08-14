<?php
class MW_Mcore_Block_Adminhtml_Notification extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_notification';
    $this->_blockGroup = 'mcore';
    $this->_headerText = Mage::helper('mcore')->__('Notification Manager');
    $this->_addButtonLabel = Mage::helper('mcore')->__('Add Notification');
    parent::__construct();
  }
  
}