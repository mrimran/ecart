<?php

class TM_Core_Block_Adminhtml_Support_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('tmcore_support');
//        Zend_Debug::dump($model->getData());

        $isNew = !$model->getId();
//        Zend_Debug::dump(__METHOD__);
        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );

        $form->setHtmlIdPrefix('module_');

        $fieldset = $form->addFieldset(
            'ticket_form',
            array('legend' => Mage::helper('tmcore')->__('Current state'))
        );

        $fieldset->addField('title', $isNew ? 'text' : 'label', array(
            'label'     => Mage::helper('tmcore')->__('Title'),
            'required'  => $isNew,
            'name'      => 'title'
        ));

        if (!$isNew) {
            $fieldset->addField('email', 'label', array(
                'label'     => Mage::helper('tmcore')->__('From'),
                'name'      => 'email'
            ));


            $fieldset->addField('id', 'hidden', array(
//                'label'     => Mage::helper('tmcore')->__('Id'),
                'name'      => 'id'
            ));
//
//            $fieldset->addField('number', 'label', array(
//                'label'     => Mage::helper('tmcore')->__('Number'),
//                'name'      => 'number'
//            ));
        }
        $dapertments = array();
        if ($model->getDepartmets() instanceof Varien_Data_Collection) {
            $dapertments = $model->getDepartmets()->toOptionArray();
        }
        $fieldset->addField('department_id', 'select', array(
            'label'     => Mage::helper('tmcore')->__('Department'),
            'name'      => 'department_id',
            'disabled'  => !$isNew,
            'required'  => $isNew,
            'values'    => $dapertments
        ));
        if (!$isNew) {
            $statuses = array();
            if ($model->getStatuses() instanceof Varien_Data_Collection) {
                $statuses = $model->getStatuses()->toOptionArray();
            }
            $fieldset->addField('status', 'select', array(
                'label'     => Mage::helper('tmcore')->__('Status'),
                'name'      => 'status',
                'disabled'  => true,
//                'disabled'  => !$isNew,
//                'required'  => $isNew,
                'values'    => $statuses
            ));
        }
        $priorities = array();
        if ($model->getPriorities() instanceof Varien_Data_Collection) {
            $priorities = $model->getPriorities()->toOptionArray();
        }
        $fieldset->addField('priority', 'select', array(
            'label'     => Mage::helper('tmcore')->__('Priority'),
            'name'      => 'priority',
            'disabled'  => !$isNew,
            'required'  => $isNew,
            'values'    => $priorities
        ));

        if (!$isNew) {
            $fieldset->addField('created_at', 'date', array(
                'label'     => Mage::helper('tmcore')->__('Create date'),
    //            'required'  => true,
                'disabled'  => true,
                'image'     => $this->getSkinUrl('images/grid-cal.gif'),
                'format'    => Varien_Date::DATETIME_INTERNAL_FORMAT,
                //Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                'name'      => 'created_at',
            ));

            $fieldset->addField('modified_at', 'date', array(
                'label'     => Mage::helper('tmcore')->__('Modified date'),
    //            'required'  => true,
                'disabled'  => true,
                'image'     => $this->getSkinUrl('images/grid-cal.gif'),
                'format'    => Varien_Date::DATETIME_INTERNAL_FORMAT,
                //Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                'name'      => 'modified_at',
            ));
        }
        if ($model->getTheards()) {
            $fieldsetTheards = $form->addFieldset(
                'ticket_theards_form',
                array('legend' => Mage::helper('tmcore')->__('Theards'))
            );
            $fieldsetTheards->addType('support_theard', 'TM_Core_Block_Adminhtml_Support_Edit_Form_Element_Theard');
            $fieldsetTheards->addField('theard', 'support_theard', array(
                'name'      => 'theard'
            ));
        }


        if (!$isNew) {
            $fieldsetAddComment = $form->addFieldset(
                'ticket_add_comment_form',
                array(
                    'legend' => Mage::helper('tmcore')->__('Add Comment')
                )
            );
        } else {
            $fieldsetAddComment = $fieldset;
        }

        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array(
            'tab_id'        => $this->getTabId(),
            'add_variables' => false,
            'add_widgets'   => false,
            'width'         => '100%',
        ));

        $fieldsetAddComment->addField('text', 'editor', array(
            'label'     => Mage::helper('tmcore')->__('Comment'),
            'name'      => 'text',
            'config'    => $wysiwygConfig,
            'wysiwyg'   => true,
            'required'  => true,
            'style'     => "width: 640px"
        ));
        $fieldsetAddComment->addField('add', 'button', array(
           'value' => Mage::helper('helpmate')->__($isNew ? 'Save' : 'Add Comment'),
           'class' => 'form-button',
           'name'  => 'add_comment_button',
           'onclick' => 'editForm.submit();return false;'
        ));

        $form->addValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('cms')->__('Main');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('cms')->__('Main');
    }

    /**
     * Returns status flag about this tab can be shown or not
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

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    /*protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('tmcore/module/' . $action);
    }*/
}
