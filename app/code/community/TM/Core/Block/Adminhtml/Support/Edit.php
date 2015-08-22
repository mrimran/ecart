<?php

class TM_Core_Block_Adminhtml_Support_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'tmcore';
        $this->_controller = 'adminhtml_support';

        parent::__construct();

        $this->setData('form_action_url', $this->getUrl('*/*/save'));
//        $this->_updateButton('save', 'label', Mage::helper('tmcore')->__('Save'));
        $this->_removeButton('delete');
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $model = Mage::registry('tmcore_support');
        if (!$model->getId()) {
            return Mage::helper('tmcore')->__(
                'Add New Ticket'
            );
        }
        return Mage::helper('tmcore')->__(
            'Ticket "%s" (#%s)', $model->getTitle(), $model->getNumber()
        );
    }
}
