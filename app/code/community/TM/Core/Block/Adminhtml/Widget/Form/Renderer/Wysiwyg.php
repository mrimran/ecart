<?php

class TM_Core_Block_Adminhtml_Widget_Form_Renderer_Wysiwyg
    extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $editor = new TM_Core_Block_Adminhtml_Widget_Form_Element_Wysiwyg($element->getData());
        $editor->setId($element->getId());
        $editor->setForm($element->getForm());
        return parent::render($editor);
    }
}
