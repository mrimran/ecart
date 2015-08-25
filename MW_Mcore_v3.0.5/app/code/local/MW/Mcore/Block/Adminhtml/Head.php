<?php
class MW_Mcore_Block_Adminhtml_Head extends Mage_Adminhtml_Block_Widget_Grid_Container
{
 public function __construct()
  {
    $this->_controller = 'adminhtml_mcore';
    $this->_blockGroup = 'mcore';
    $this->_headerText = Mage::helper('mcore')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('mcore')->__('Add Item');
    parent::__construct();  
  }
  
  public function getBackendUrl()
  {
      return Mage::helper("adminhtml")->getUrl("mcore/mcore/extendtrial/"); // can co token ??
  }
}