<?php
class TM_Core_Block_Adminhtml_Support_Edit_Form_Element_Theard extends Varien_Data_Form_Element_Abstract
{
    public function getElementHtml()
    {
        return $this->getContentHtml();
    }

    /**
     * Prepares content block
     *
     * @return string
     */
    public function getContentHtml()
    {
//        return '--- THEARD --';
//        /* @var $content TM_Helpmate_Block_Adminhtml_Ticket_Edit_Form_Element_Theard_Content */
//        Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Gallery_Content
        $content = Mage::getSingleton('core/layout')
            ->createBlock('tmcore/adminhtml_support_edit_form_element_theard_content');
//
        $content->setId($this->getHtmlId() . '_content')
            ->setElement($this);

        return $content->toHtml();
    }

    public function getLabel()
    {
        return '';
    }

    public function toHtml()
    {
        return '<tr><td class="value" style="width:200%" colspan="3">' .
            $this->getElementHtml() .
        '</td></tr>';
    }
}
