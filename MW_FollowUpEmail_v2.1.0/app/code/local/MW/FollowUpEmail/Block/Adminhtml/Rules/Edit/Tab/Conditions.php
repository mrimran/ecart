<?php

class MW_FollowUpEmail_Block_Adminhtml_Rules_Edit_Tab_Conditions

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

        return Mage::helper('followupemail')->__('Additional Conditions');

    }



    /**

     * Prepare title for tab

     *

     * @return string

     */

    public function getTabTitle()

    {

        return Mage::helper('followupemail')->__('Additional Conditions');

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

		

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')

            ->setTemplate('promo/fieldset.phtml')

            ->setNewChildUrl($this->getUrl('adminhtml/promo_quote/newConditionHtml/form/rule_conditions_fieldset'));

		//echo $this->getUrl('adminhtml/promo_quote/newConditionHtml/form/rule_conditions_fieldset'); 

		

        $fieldset = $form->addFieldset('conditions_fieldset', array(

            'legend'=>Mage::helper('followupemail')->__('Apply the rule only if the following conditions are met (leave blank for no conditions)')

        ))->setRenderer($renderer);



        $fieldset->addField('conditions', 'text', array(

            'name' => 'conditions',

            'label' => Mage::helper('followupemail')->__('Conditions'),

            'title' => Mage::helper('followupemail')->__('Conditions'),
			
			'after_element_html' => 

                    '<span class="note"><small>'

                        .Mage::helper('followupemail')->__('The conditions that do not relate to the event will be ignored')

                    .'</small></span>',

        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));
		$fieldset->addField('note', 'note', array(
          'text'     => Mage::helper('followupemail')->__(''),
		  'after_element_html' => '</br><span class="note"><small>'

                        .Mage::helper('followupemail')->__('* The conditions that do not relate to the event will be ignored')

                    .'</small></span>',
        ));
        //Mage::log(get_class(Mage::getBlockSingleton('rule/conditions')));

        $form->setValues($model->getData());

		

        //$form->setUseContainer(true);



        $this->setForm($form);



        return parent::_prepareForm();

    }

}

