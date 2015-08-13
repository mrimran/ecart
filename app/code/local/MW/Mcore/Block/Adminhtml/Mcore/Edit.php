<?php

class MW_Mcore_Block_Adminhtml_Mcore_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'mcore';
        $this->_controller = 'adminhtml_mcore';
        
        $this->_updateButton('save', 'label', Mage::helper('mcore')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('mcore')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('mcore_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'mcore_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'mcore_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('mcore_data') && Mage::registry('mcore_data')->getId() ) {
            return Mage::helper('mcore')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('mcore_data')->getTitle()));
        } else {
            return Mage::helper('mcore')->__('Add Item');
        }
    }
}