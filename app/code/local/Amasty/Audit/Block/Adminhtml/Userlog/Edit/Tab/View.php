<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */   
class Amasty_Audit_Block_Adminhtml_Userlog_Edit_Tab_View  extends Mage_Adminhtml_Block_Template
{
    protected $_log;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amaudit/tab/view.phtml');
        $this->setChild('details',  Mage::app()->getLayout()->createBlock('amaudit/adminhtml_userlog_edit_tab_view_details'));
    }

    public function getLog()
    {
        if (!$this->_log) {
            $this->_log = Mage::registry('current_log');
            $this->_log->setDateTime(Mage::getModel('core/date')->date(null, $this->_log->getDateTime()));
        }
        return $this->_log;
    }

    public function isRestorable($log)
    {
        $isRestorable = false;
        $notRestorableModels = array('Mage_Sales_Model_Order_Status_History', 'Mage_Tax_Model_Calculation_Rule');
        if ($log->getType() == 'Edit') {
            $models = $this->_getDetailsModels($log->getEntityId());
            foreach ($models as $model) {
                try {
                    $elementModel = Mage::getModel($model);
                    if (!is_object($elementModel)) {
                        continue;
                    }
                    $elementData = $elementModel->load($log->getElementId())->getData();
                    $pureElementData = $this->_pureArray($elementData);

                    if (in_array($model, $notRestorableModels)) {
                        break;
                    }

                    if (!empty($pureElementData)) {
                        $isRestorable = true;
                    }
                } catch (Exception $e) {
                }
            }
        }

        return $isRestorable;
    }

    protected function _getDetailsModels($logId)
    {
        $models = array();
        $collectionDetails = Mage::getModel('amaudit/log_details')->getCollection();
        $collectionDetails->getSelect()
            ->where('log_id = ?', $logId)
        ;
        foreach ($collectionDetails as $detail) {
            if (!in_array($detail->getModel(), $models)) {
                $models[] = $detail->getModel();
            }
        }
        return $models;
    }

    protected function _pureArray($array)
    {
        if (isset($array['created_at'])) {
            unset($array['created_at']);
        }

        foreach ($array as $key => $element) {
            if (is_array($element)) {
                $element = $this->_pureArray($element);
            }
            if (empty($element)) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}
