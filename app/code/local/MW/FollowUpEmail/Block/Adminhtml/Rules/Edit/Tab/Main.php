<?php

class MW_FollowUpEmail_Block_Adminhtml_Rules_Edit_Tab_Main

extends Mage_Adminhtml_Block_Widget_Form

implements Mage_Adminhtml_Block_Widget_Tab_Interface

{

	/**

	* Prepare content for tab

	*

	* @return string

	*/

	public function getTabLabel()

	{

		return Mage::helper('followupemail')->__('Rule Information');

	}

	

	/**

	* Prepare title for tab

	*

	* @return string

	*/

	public function getTabTitle()

	{

		return Mage::helper('followupemail')->__('Rule Information');

	}

	

	/**

	* Returns status flag about this tab can be showen or not

	*

	* @return true

	*/

	public function canShowTab()

	{

		return true;

	}

	

	/**

	* Returns status flag about this tab hidden or not

	*

	* @return true

	*/

	public function isHidden()

	{

		return false;

	}

	

	protected function _prepareForm()

	{
		$model = Mage::registry('current_fue_rule');
		$form = new Varien_Data_Form();

      $this->setForm($form);

      $fieldset = $form->addFieldset('rules_form', array('legend'=>Mage::helper('followupemail')->__('General')));
		if($model->getId()){
			$fieldset->addField('rule_id', 'hidden', array(

          'label'     => Mage::helper('followupemail')->__('Id'),

          'required'  => false,

          'name'      => 'rule_id',

      ));	
		}     	
		
	
		

      $fieldset->addField('title', 'text', array(

          'label'     => Mage::helper('followupemail')->__('FUE Rule Title'),

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
		  'after_element_html' => 

                    '</br><span class="note"><small>'

                        .$this->__('Enable and save Rule to activate')

                    .'</small></span>',

      ));

	  

	  $customerGroups = Mage::getResourceModel('customer/group_collection')

			->load()->toOptionArray();		

			

	  $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

	  

	  $fieldset->addField('from_date', 'date', array(

		'name'   => 'from_date',

		'label'  => Mage::helper('followupemail')->__('Active From'),

		'title'  => Mage::helper('followupemail')->__('Active From'),

		'image'  => $this->getSkinUrl('images/grid-cal.gif'),

		'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,

		'format'       => $dateFormatIso,
		'after_element_html' => 

                    '</br><span class="note"><small>'

                        .$this->__('Leave blank if no limit')

                    .'</small></span>',

		));			

     

      $fieldset->addField('to_date', 'date', array(

		'name'   => 'to_date',

		'label'  => Mage::helper('followupemail')->__('Active To'),

		'title'  => Mage::helper('followupemail')->__('Active To'),

		'image'  => $this->getSkinUrl('images/grid-cal.gif'),

		'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,

		'format'       => $dateFormatIso

		));

		

		

		$fieldset->addField('event', 'select', array(

		'name'       => 'event',

		'label'      => Mage::helper('followupemail')->__('Send FUE when'),

		'required'   => false,

		'values'    => Mage::getSingleton('followupemail/system_config_eventfollowupemail')->toOptionArray(false),

		'onchange' => 'doCheckEventType()',

		));

				

		$fieldset->addField('cancel_event', 'multiselect', array(

		'name'      => 'cancel_event[]',

		'label'     => Mage::helper('followupemail')->__('Cancel Pending Emails If'),

		'title'     => Mage::helper('followupemail')->__('Cancel Pending Emails If'),

		'required'  => false,

		'values'    => Mage::getSingleton('followupemail/system_config_eventfollowupemail')->toOptionArray(true),		

		'after_element_html' => 

                    '</br><span class="note"><small>'

                        .$this->__("Only pending emails will be cancelled. Use 'ctrl' to select more than 1 event")

                    .'</small></span>',

		));	

		

		//Store View

  	  if (!Mage::app()->isSingleStoreMode()) {

              $fieldset->addField('store_ids', 'multiselect', array(

                    'name'      => 'store_ids[]',

                    'label'     => Mage::helper('followupemail')->__('Store View'),

                    'title'     => Mage::helper('followupemail')->__('Store View'),

                    'required'  => true,

                    'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),               

              ));

       } 

        else {

            $fieldset->addField('store_ids', 'hidden', array(

                'name'      => 'store_ids[]',

                'value'     => Mage::app()->getStore(true)->getId()

            ));           

        }

		

		$fieldset->addField('customer_group_ids', 'multiselect', array(

		'name'      => 'customer_group_ids[]',

		'label'     => Mage::helper('followupemail')->__('Customer Groups'),

		'title'     => Mage::helper('followupemail')->__('Customer Groups'),

		'required'  => true,

		'values'    => $customerGroups,

		));		
		
		$fieldset->addField('send_mail_customer', 'select', array(

	          'label'     => Mage::helper('followupemail')->__('Send email to customer'),

	          'name'      => 'send_mail_customer',

	          'values'    => array(

	              array(

	                  'value'     => 1,

	                  'label'     => Mage::helper('followupemail')->__('Yes, send to all customers'),

	              ),



	              array(

	                  'value'     => 2,

	                  'label'     => Mage::helper('followupemail')->__('Yes, send only to newsletter subscribers'),

	              ),
				  
				  array(

	                  'value'     => 3,

	                  'label'     => Mage::helper('followupemail')->__('No'),

	              ),

	          ),
			  
			  'after_element_html' => 

                    '</br><span class="note"><small>'

                        .$this->__("Select 'no' if only admin to receive notification of an event")

                    .'</small></span>',

      	));

				

        $fieldset->addField('email_chain', 'text', array(

        'label'     => Mage::helper('followupemail')->__('Email Schedule'),

		'title'     => Mage::helper('followupemail')->__('Email Schedule'),

        'name'      => 'email_chain',

        'required'  => true,

        'class'     => 'requried-entry'

    	));
		
		$url = Mage::helper("adminhtml")->getUrl("*/followupemail_rules/applyoldbackdata");

		$testButton = $this->getLayout()

                ->createBlock('adminhtml/widget_button')

                ->setData(array(

            'id' => 'applyoldback',

            'label' => Mage::helper('followupemail')->__('Apply to past orders'),

            'class' => 'save',

			'value' => $url,

			'onclick' => 'applyoldbackdata(this.value)'

                ));

        $fieldset->addField('send_button', 'note', array(

            'text' => $testButton->toHtml(),
			'after_element_html' => 

                    '</br><span class="note" id="noteapplyoldback"><small>'

                        .$this->__('Apply retroactively to past orders within the email schedule time period')

                    .'</small></span>',

        ));		

        $form->getElement('email_chain')->setRenderer($this->getLayout()->createBlock('followupemail/adminhtml_rules_edit_tab_renderer_chain'));	
	  
	  

      if ( Mage::getSingleton('adminhtml/session')->getRulesData() )

      {

          $form->setValues(Mage::getSingleton('adminhtml/session')->getRulesData());

          Mage::getSingleton('adminhtml/session')->setRulesData(null);

      } elseif ( Mage::registry('rules_data') ) {

          $form->setValues(Mage::registry('rules_data')->getData());

      }

	  $this->setForm($form);

      return parent::_prepareForm();

	}

}

