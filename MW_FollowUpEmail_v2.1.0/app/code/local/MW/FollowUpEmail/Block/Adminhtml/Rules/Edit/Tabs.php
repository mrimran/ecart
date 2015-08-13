<?php

class MW_FollowUpEmail_Block_Adminhtml_Rules_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('rules_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('followupemail')->__('Rule Information'));
  }

  /*protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('followupemail')->__('General'),
          'title'     => Mage::helper('followupemail')->__('General'),
          'content'   => $this->getLayout()->createBlock('followupemail/adminhtml_rules_edit_tab_form')->toHtml(),
      ));	
	 
      return parent::_beforeToHtml();
  }*/
}