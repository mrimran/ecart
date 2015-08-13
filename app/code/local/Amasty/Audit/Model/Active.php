<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_Model_Active extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('amaudit/active', 'entity_id');
    }

    public function saveActive($data)
    {
        try
        {
            $activeData = array(
                'session_id' => session_id(),
                'recent_activity' => $data['date_time'],
            );

            $allData = array_merge($data, $activeData);

            $this->setData($allData);
            $this->save();
        }
        catch (Exception $e)
        {
            Mage::logException($e);
            Mage::log($e->getMessage());
        }
    }

    public function onAdminLogout()
    {
        $sessionId = Mage::getModel('core/cookie')->get('adminhtml');
        $this->removeOnlineAdmin($sessionId);
    }

    public function removeOnlineAdmin($sessionId)
    {
        $activeEntity = $this->getActiveEntity($sessionId);
        $activeEntity->delete();
        Mage::getModel('amaudit/visit')->endVisit($sessionId);
    }

    public function saveSomeEvent()
    {
        if (Mage::app()->getStore()->isAdmin()) {
            $sessionId = Mage::getModel('core/cookie')->get('adminhtml');
            $this->updateOnlineAdminActivity($sessionId);
        }
    }

    public function updateOnlineAdminActivity($sessionId)
    {
        $time = Mage::getModel('core/date')->timestamp();
        $timeStamp = date('Y-m-d H:i:s', $time);
        $activeEntity = $this->getActiveEntity($sessionId);
        $activeEntityData = $activeEntity->getData();
        if (!empty($activeEntityData)) {
            $activeEntity->setData('recent_activity', $timeStamp);
            $activeEntity->save();
        }
    }

    public function getActiveEntity($sessionId)
    {
        $activeModel = Mage::getModel('amaudit/active')->getCollection()
            ->addFieldToFilter('session_id', $sessionId);
        $activeEntity = $activeModel->getFirstItem();
        return $activeEntity;
    }

    public function checkOnline()
    {
        $collection = $this->getCollection();
        $sessionLifeTime = Mage::getStoreConfig('admin/security/session_cookie_lifetime');
        if (empty($sessionLifeTime)) {
            $sessionLifeTime = 3600;
        }
        $currentTime = Mage::getModel('core/date')->timestamp(time());
        foreach ($collection as $admin) {
            $rowTime = strtotime($admin->getRecentActivity());
            $timeDifference = $currentTime - $rowTime;
            if ($timeDifference >= $sessionLifeTime) {
                $sessionId = $admin->getSessionId();
                $this->removeOnlineAdmin($sessionId);
            }
        }
    }

    public function destroySession($sessionId)
    {
        if (isset($_SESSION)) {
            session_id($sessionId);
            session_destroy();
        }
    }
}