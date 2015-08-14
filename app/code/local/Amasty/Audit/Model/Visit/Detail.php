<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */


class Amasty_Audit_Model_Visit_Detail extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('amaudit/visit_detail', 'detail_id');
    }

    public function getLastSessionPage($sessionId)
    {
        $lastItem = NULL;
        $visitDetailsCollection = Mage::getModel('amaudit/visit_detail')->getCollection();
        $visitDetailsCollection->getSelect()
            ->where("session_id = (?)", $sessionId);
        $lastItem = $visitDetailsCollection->getLastItem();

        return $lastItem;
    }

    public function saveLastPageDuration($sessionId)
    {
        $lastPage = $this->getLastSessionPage($sessionId);
        $lastPageData = $lastPage->getData();
        $time = time();

        $lastPageTime = Mage::getSingleton('core/session')->getLastPageTime();

        if (!empty($lastPageData) && $lastPageTime) {
            $duration = $time - $lastPageTime;
            $lastPage->setStayDuration($duration);
            $lastPage->save();
        }
    }
}