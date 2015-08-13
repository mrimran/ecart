<?php

class MW_FollowUpEmail_Block_Adminhtml_Rules_Edit_Tab_Coupons

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

        return Mage::helper('followupemail')->__('Coupons');

    }



    /**

     * Prepare title for tab

     *

     * @return string

     */

    public function getTabTitle()

    {

        return Mage::helper('followupemail')->__('Coupons');

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

        $model = Mage::registry('rules_data');		

		//$model = Mage::getModel('salesrule/rule');

        $form = new Varien_Data_Form();



        $form->setHtmlIdPrefix('rule_');		        

		$fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('followupemail')->__('Coupons')));		

		$fieldset->addField('coupon_status', 'select', array(

          'label'     => Mage::helper('followupemail')->__('Enable coupons'),

          'name'      => 'coupon_status',

          'values'    => array(
		  
			array(

                  'value'     => 2,

                  'label'     => Mage::helper('followupemail')->__('Disabled'),

              ),

              array(

                  'value'     => 1,

                  'label'     => Mage::helper('followupemail')->__('Enabled'),

              ),

          ),
		  'after_element_html' => 

                    '</br><span class="note"><small>'

                        .$this->__('Enable coupons for this rule')

                    .'</small></span>',

      ));
		$note = "Create Shopping Cart Price Rules in <a target='_blank' href=".Mage::helper("adminhtml")->getUrl("adminhtml/promo_quote/index").">here</a>";
		$fieldset->addField('coupon_sales_rule_id', 'select', array(

		'name'      => 'coupon_sales_rule_id',

		'label'     => Mage::helper('followupemail')->__('Shopping Cart Price Rule'),

		'title'     => Mage::helper('followupemail')->__('Shopping Cart Price Rule'),

		'required'  => false,

		'values'    => Mage::getSingleton('followupemail/system_config_shoppingcartrule')->toOptionArray(),		
		
		'note'		=> $note,

		/*'after_element_html' => 

                    '</br><span class="note"><small>'

                        .$this->__("Only pending emails will be cancelled. Use 'ctrl' to select more than 1 event")

                    .'</small></span>',
*/
		));
		
		$fieldset->addField('coupon_prefix', 'text', array(

        'label'     => Mage::helper('followupemail')->__('Coupon Code Prefix'),

		'title'     => Mage::helper('followupemail')->__('Coupon Code Prefix'),

        'name'      => 'coupon_prefix',

        'required'  => false,

        'class'     => 'requried-entry'

    	));	
		
		$fieldset->addField('coupon_expire_days', 'text', array(

        'label'     => Mage::helper('followupemail')->__('Coupon expires after # (days)'),

		'title'     => Mage::helper('followupemail')->__('Coupon expires after # (days)'),

        'name'      => 'coupon_expire_days',

        'required'  => false,

        'class'     => 'requried-entry'

    	));		
		
		/*$fieldset->addField('note', 'note', array(
          'text'     => Mage::helper('followupemail')->__('Ensure shopping cart price rule is active and ‘specific coupon’ field is selected. A ‘unique’ coupon will be generated using the same conditions and actions of rule for EACH applicable email (for one time use by customer).'),		  
        ));*/
		
		
        $form->setValues($model->getData());



        $this->setForm($form);



        return parent::_prepareForm();

    }

}

