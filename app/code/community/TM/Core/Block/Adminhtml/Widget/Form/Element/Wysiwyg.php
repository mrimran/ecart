<?php

class TM_Core_Block_Adminhtml_Widget_Form_Element_Wysiwyg extends Varien_Data_Form_Element_Textarea
{
    /**
     * Retrieve additional html and put it at the end of element html
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $html = parent::getAfterElementHtml();
        if ($this->getIsWysiwygEnabled()) {
            $disabled = ($this->getDisabled() || $this->getReadonly());
            $html .= Mage::getSingleton('core/layout')
                ->createBlock('adminhtml/widget_button', '', array(
                    'label'   => Mage::helper('catalog')->__('WYSIWYG Editor'),
                    'type'    => 'button',
                    'disabled' => $disabled,
                    'class' => /*($disabled) ? 'disabled btn-wysiwyg' : */'btn-wysiwyg',
                    'onclick' => 'catalogWysiwygEditor.open(\''.Mage::helper('adminhtml')->getUrl('*/*/wysiwyg').'\', \''.$this->getHtmlId().'\')'
                ))->toHtml();
        }
        return $html;
    }

    /**
     * Check whether wysiwyg enabled or not
     *
     * @return boolean
     */
    public function getIsWysiwygEnabled()
    {
        $helper = Mage::helper('catalog');

        if (method_exists($helper, 'isModuleEnabled')) {
            if (Mage::helper('catalog')->isModuleEnabled('Mage_Cms')) {
                return (bool)(Mage::getSingleton('cms/wysiwyg_config')->isEnabled());
            }
        } else {
            return (bool)(Mage::getSingleton('cms/wysiwyg_config')->isEnabled()); // Magento 1401-1420
        }

        return false;
    }
}

