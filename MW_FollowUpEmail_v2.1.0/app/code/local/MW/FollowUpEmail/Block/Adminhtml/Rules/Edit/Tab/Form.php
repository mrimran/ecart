<?php

class MW_FollowUpEmail_Block_Adminhtml_Rules_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('rules_form', array('legend'=>Mage::helper('followupemail')->__('General')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('followupemail')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
		
      $fieldset->addField('is_active', 'select', array(
          'label'     => Mage::helper('followupemail')->__('Status'),
          'name'      => 'is_active',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('followupemail')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('followupemail')->__('Disabled'),
              ),
          ),
      ));
	  
	  $customerGroups = Mage::getResourceModel('customer/group_collection')
			->load()->toOptionArray();
	  $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
	  
	  $fieldset->addField('from_date', 'date', array(
		'name'   => 'from_date',
		'label'  => Mage::helper('followupemail')->__('From Date'),
		'title'  => Mage::helper('followupemail')->__('From Date'),
		'image'  => $this->getSkinUrl('images/grid-cal.gif'),
		'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
		'format'       => $dateFormatIso
		));
     
      $fieldset->addField('to_date', 'date', array(
		'name'   => 'to_date',
		'label'  => Mage::helper('followupemail')->__('To Date'),
		'title'  => Mage::helper('followupemail')->__('To Date'),
		'image'  => $this->getSkinUrl('images/grid-cal.gif'),
		'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
		'format'       => $dateFormatIso
		));
		
		
		$fieldset->addField('event', 'select', array(
		'name'       => 'event',
		'label'      => Mage::helper('followupemail')->__('Event'),
		'required'   => true,
		//'options'    => Mage::getModel('followupemail/event')->getCouponTypes(),
		));
				
		$fieldset->addField('cancel_events', 'multiselect', array(
		'name'      => 'cancel_events[]',
		'label'     => Mage::helper('followupemail')->__('Customer Groups'),
		'title'     => Mage::helper('followupemail')->__('Customer Groups'),
		'required'  => true,
		'values'    => $customerGroups,
		));	
		
		$fieldset->addField('customer_group_ids', 'multiselect', array(
		'name'      => 'customer_group_ids[]',
		'label'     => Mage::helper('followupemail')->__('Cancellation events'),
		'title'     => Mage::helper('followupemail')->__('Cancellation events'),
		'required'  => false,
		'values'    => $customerGroups,
		));	
     
      if ( Mage::getSingleton('adminhtml/session')->getRulesData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getRulesData());
          Mage::getSingleton('adminhtml/session')->setRulesData(null);
      } elseif ( Mage::registry('rules_data') ) {
          $form->setValues(Mage::registry('rules_data')->getData());
      }
      return parent::_prepareForm();
  }
}