<?php
class MW_FollowUpEmail_Block_Adminhtml_Rules_Edit_Tab_Googleanalytics
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function getTabLabel()
    {
        return Mage::helper('followupemail')->__('Google Analytics');
    }

    public function getTabTitle()
    {
        return Mage::helper('followupemail')->__('Google Analytics');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('rules_data');
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('followupemail')->__('Google Analytics Tracking Code')));

        $fieldset->addField('campaign_note', 'note', array(
            'class'    => 'fue_note',
            'text'     => Mage::helper('followupemail')->__('Note that Google Analytics should be configured and activated use this feature')
        ));

        $fieldset->addField('campaign_source', 'text', array(
            'label'     => Mage::helper('followupemail')->__('Campaign Source'),
            'title'     => Mage::helper('followupemail')->__('Campaign Source'),
            'name'      => 'campaign_source',
            'required'  => false,
            'class'     => 'requried-entry',
            'note'          => '(utm_source = only included if you supply a source term)'
        ));

        $fieldset->addField('campaign_medium', 'text', array(
            'label'     => Mage::helper('followupemail')->__('Campaign Medium'),
            'title'     => Mage::helper('followupemail')->__('Campaign Medium'),
            'name'      => 'campaign_medium',
            'required'  => false,
            'class'     => 'requried-entry',
            'note'          => '(utm_medium = email)'
        ));

        $fieldset->addField('campaign_term', 'text', array(
            'label'     => Mage::helper('followupemail')->__('Campaign Term'),
            'title'     => Mage::helper('followupemail')->__('Campaign Term'),
            'name'      => 'campaign_term',
            'required'  => false,
            'class'     => 'requried-entry',
            'note'          => 'utm_term = the link text, or alt attribute (for images)'
        ));

        $fieldset->addField('campaign_content', 'text', array(
            'label'     => Mage::helper('followupemail')->__('Campaign Content'),
            'title'     => Mage::helper('followupemail')->__('Campaign Content'),
            'name'      => 'campaign_content',
            'required'  => false,
            'class'     => 'requried-entry',
            'note'          => 'utm_content = the email campaign name plus a unique campaign identifier (CID)'
        ));

        $fieldset->addField('campaign_name', 'text', array(
            'label'     => Mage::helper('followupemail')->__('Campaign Name'),
            'title'     => Mage::helper('followupemail')->__('Campaign Name'),
            'name'      => 'campaign_name',
            'required'  => false,
            'class'     => 'requried-entry',
            'note'          => 'utm_campaign = the email campaign name'
        ));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
