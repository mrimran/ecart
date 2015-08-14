<?php

class TM_Core_Block_Adminhtml_Widget_Form_Element_Wysiwyg_Content
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Modified Mage_Adminhtml_Block_Catalog_Helper_Form_Wysiwyg_Content to allow to use widgets
     *
     * @return Mage_Adminhtml_Block_Catalog_Helper_Form_Wysiwyg_Content
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'wysiwyg_edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        $config['document_base_url']     = $this->getData('store_media_url');
        $config['store_id']              = $this->getData('store_id');
        $config['add_variables']         = true;
        $config['add_widgets']           = true;
        $config['add_directives']        = true;
        $config['use_container']         = true;
        $config['container_class']       = 'hor-scroll';
        // $config['enabled']               = true;
        // $config['hidden']                = true; // widget popup doesn't works if wysiwyg is visible by default

        $form->addField($this->getData('editor_element_id'), 'editor', array(
            'name'      => 'content',
            'style'     => 'width:725px;height:460px',
            'required'  => true,
            'force_load' => true,
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig($config)
        ));
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
