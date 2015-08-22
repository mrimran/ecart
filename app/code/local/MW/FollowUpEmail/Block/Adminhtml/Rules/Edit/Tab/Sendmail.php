<?php

class MW_FollowUpEmail_Block_Adminhtml_Rules_Edit_Tab_Sendmail

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

        return Mage::helper('followupemail')->__('Sender Information');

    }



    /**

     * Prepare title for tab

     *

     * @return string

     */

    public function getTabTitle()

    {

        return Mage::helper('followupemail')->__('Sender Information');

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

		$fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('followupemail')->__('Send Email')));

		$fieldset->addField('sender_name', 'text', array(

                'name' => 'sender_name',

                'label' => Mage::helper('followupemail')->__('Sender Name'),

                'title' => Mage::helper('followupemail')->__('Sender Name'),

                'after_element_html' => 

                    '<span class="note"><small>'

                        .Mage::helper('followupemail')->__('Leave blank to use the default sender (set up in configuration tab)')

                    .'</small></span>',

            ));

		

		$fieldset->addField('sender_email', 'text', array(

                'name' => 'sender_email',

                'label' => Mage::helper('followupemail')->__('Sender Email'),

                'title' => Mage::helper('followupemail')->__('Sender Email'),

                'after_element_html' => 

                    '<span class="note"><small>'

                        .Mage::helper('followupemail')->__('Leave blank to use the default sender (set up in configuration tab)')

                    .'</small></span>',

            ));		

		

		/*$fieldset->addField('only_newsletter_subscribers', 'select', array(

	          'label'     => Mage::helper('followupemail')->__('Send only to newsletter subscribers'),

	          'name'      => 'only_newsletter_subscribers',

	          'values'    => array(

			   array(

	                  'value'     => 2,

	                  'label'     => Mage::helper('followupemail')->__('No'),

	              ),

	              array(

	                  'value'     => 1,

	                  'label'     => Mage::helper('followupemail')->__('Yes'),

	              ),	            

	          ),

      	));*/

		

		$fieldset->addField('copy_to_email', 'text', array(

                'name' => 'copy_to_email',

                'label' => Mage::helper('followupemail')->__('Send a copy to'),

                'title' => Mage::helper('followupemail')->__('Send a copy to'),

				'after_element_html' => 

                    '<span class="note"><small>'

                        .Mage::helper('followupemail')->__('BCC to, emails are separated by comma ","')

                    .'</small></span>',

                //'required' => true,

            ));        

		

		

        $form->setValues($model->getData());

		

        //$form->setUseContainer(true);



        $this->setForm($form);



        return parent::_prepareForm();

    }

}

