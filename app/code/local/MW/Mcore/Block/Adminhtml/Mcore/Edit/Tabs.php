<?php

class MW_Mcore_Block_Adminhtml_Mcore_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('mcore_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('mcore')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('mcore')->__('Item Information'),
          'title'     => Mage::helper('mcore')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('mcore/adminhtml_mcore_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}