<?php
class MW_FollowUpEmail_Block_Adminhtml_Rules_Edit_Tab_Renderer_Chain 
extends Mage_Adminhtml_Block_Widget 
implements Varien_Data_Form_Element_Renderer_Interface 
{
    public function __construct() {
        $this->setTemplate('mw_followupemail/emailchain.phtml');
    }

    public function isMultiWebsites() {
        return !Mage::app()->isSingleStoreMode();
    }

    public function getEmailTemplates() 
    {
        $result = array(0 => $this->__('--- Select Template ---'));
        $result = array_merge($result, Mage::getModel('followupemail/system_config_emailtemplate')->getEmailTemplates());
        return $result;
    }

    public function getValues() {
        $__data = $this->getElement()->getValue();		
        if (!is_array($__data)) $__data = array();
        return $__data;
    }

    protected function _prepareLayout() {
        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => $this->__('Add email'),
                    'onclick'   => 'emailsControl.addItem()',
                    'class' => 'add'
                )
            )
        );
      return parent::_prepareLayout();
    }

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);
        return $this->toHtml();
    }

    public function getAddButtonHtml() {
        return $this->getChildHtml('add_button');
    }
}