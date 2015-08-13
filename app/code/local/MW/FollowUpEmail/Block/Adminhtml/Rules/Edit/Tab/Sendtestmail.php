<?php

class MW_FollowUpEmail_Block_Adminhtml_Rules_Edit_Tab_Sendtestmail

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

        return Mage::helper('followupemail')->__('Send Test Email');

    }



    /**

     * Prepare title for tab

     *

     * @return string

     */

    public function getTabTitle()

    {

        return Mage::helper('followupemail')->__('Send Test Email');

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

		$fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('followupemail')->__('Send Test Email')));		

		$fieldset->addField('test_recipient', 'text', array(

                'name' => 'testemail[test_recipient]',

                'label' => Mage::helper('followupemail')->__('Send test email to'),

                'title' => Mage::helper('followupemail')->__('Send test email to'),

                //'required' => true,

            ));

		

        $fieldset->addField('test_customer_name', 'text', array(

                'name' => 'testemail[test_customer_name]',

                'label' => Mage::helper('followupemail')->__('Test customer email'),

                'title' => Mage::helper('followupemail')->__('Test customer email'),

				'after_element_html' => 

                    '<span class="note"><small>'

                        .Mage::helper('followupemail')->__('Select customer email address for test email (customer will not receive this email)')

                    .'</small></span>',

                //'required' => true,

            ));		

			

		$fieldset->addField('test_order_id', 'text', array(

                'name' => 'testemail[test_order_id]',

                'label' => Mage::helper('followupemail')->__('Test Order number'),

                'title' => Mage::helper('followupemail')->__('Test Order number'),

				'after_element_html' => 

                    '<span class="note"><small>'

                        .Mage::helper('followupemail')->__('Select order # for test email (customer will not receive this email)')

                    .'</small></span>',

                //'required' => true,

            ));

			

		$url = Mage::helper("adminhtml")->getUrl("*/followupemail_rules/test");

		$testButton = $this->getLayout()

                ->createBlock('adminhtml/widget_button')

                ->setData(array(

            'id' => 'sendtestemail',

            'label' => Mage::helper('followupemail')->__('Send Test Email'),

            'class' => 'save',

			'value' => $url,

			'onclick' => 'sendTest(this.value)'

                ));

        $fieldset->addField('send_button', 'note', array(

            'text' => $testButton->toHtml(),

        ));			

		

        $form->setValues($model->getData());

		

        //$form->setUseContainer(true);



        $this->setForm($form);



        return parent::_prepareForm();

    }

}

