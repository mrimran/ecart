<?php

class MW_Mcore_Block_Adminhtml_Notification_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('notification_form', array('legend'=>Mage::helper('mcore')->__('Item information')));
     
  
	  $fieldset->addField('message', 'editor', array(
          'name'      => 'message',
          'label'     => Mage::helper('mcore')->__('Message'),
          'title'     => Mage::helper('mcore')->__('Message'),
          'style'     => 'width:700px; height:100px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
      
    $fieldset->addField('time_apply', 'text', array(
          'label'     => Mage::helper('mcore')->__('Time Apply(days)'),
          'class'     => 'required-entry validate-number',
          'required'  => true,
          'name'      => 'time_apply',
      ));
           
   
           
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('mcore')->__('Status'),
          'name'      => 'status',
          'values'    => array(
      		
      		array(
                  'value'     => 0,
                  'label'     => Mage::helper('mcore')->__('Normal'),
              ),
              
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('mcore')->__('Remind'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('mcore')->__('Not Display'),
              ),
          ),
      ));
          
      if ( Mage::getSingleton('adminhtml/session')->getMcoreData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getMcoreData());
          Mage::getSingleton('adminhtml/session')->setMcoreData(null);
      } elseif ( Mage::registry('mcore_data') ) {
          $form->setValues(Mage::registry('mcore_data')->getData());
      }
      return parent::_prepareForm();
  }
}