<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */


class Amasty_Audit_Model_Visit extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('amaudit/visit', 'visit_id');
    }

    public function startVisit($userData)
    {
        $enableVisit = Mage::getStoreConfig('amaudit/log/enableVisitHistory');

        if ($enableVisit && !empty($userData['username'])) {
            try {
                $userData['session_start'] = time();
                $userData['session_id'] = session_id();
                $this->setData($userData);
                $this->save();
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::log($e->getMessage());
            }
        }
    }

    public function endVisit($sessionId)
    {
        $visitEntity = $this->load($sessionId, 'session_id');
        $visitEntity->addData(array('session_end' => time()));
        $visitEntity->save();

        $detailModel = Mage::getModel('amaudit/visit_detail');

        $detailModel->saveLastPageDuration(session_id());
    }
}