<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_Model_Data extends Mage_Core_Model_Abstract
{
    const SUCCESS = 1;
    const UNSUCCESS = 0;
    const LOCKED = 2;
    const LOGOUT = 3;
    const MIN_UNSUCCESSFUL_COUNT = 5;
    const MIN_ALL_COUNT = 5;
    const WEEK = 604800;

    public function _construct()
    {
        $this->_init('amaudit/data', 'entity_id');
    }

    /**
     * If email did not send - return count of unsuccessful login for last hour .
     * @return int
     */
    public function getUnsuccessfulCount()
    {
        $latestSending = Mage::getStoreConfig('amaudit/unsuccessful_log_mailing/latest_sending');
        $duration = 3600;
        $time = Mage::getModel('core/date')->gmtDate();
        $intTime = strtotime($time);
        $count = 0;
        if (($intTime - $latestSending) > $duration) {
            $unsuccessfulDataCollection = Mage::getModel('amaudit/data')->getCollection();
            $lastHour = $intTime - $duration;
            $fromHour = date('Y-m-d H:i:s', $lastHour);
            $unsuccessfulDataCollection
                ->addFieldToFilter('date_time', array('from' => $fromHour, 'to' => $time))
                ->addFieldToFilter('status', Amasty_Audit_Model_Data::UNSUCCESS);

            $count = $unsuccessfulDataCollection->count();
        }

        if ($count >= Amasty_Audit_Model_Data::MIN_UNSUCCESSFUL_COUNT) {
            Mage::getConfig()->saveConfig('amaudit/unsuccessful_log_mailing/latest_sending', $intTime);
            Mage::getConfig()->cleanCache();
        }

        return $count;
    }

    public function isSuspicious($userData)
    {
        $time = Mage::getModel('core/date')->gmtDate();
        $intTime = strtotime($time);
        $intlastTime = $intTime - Amasty_Audit_Model_Data::WEEK;
        $lastTime = date('Y-m-d H:i:s', $intlastTime);
        $allCollection = Mage::getModel('amaudit/data')->getCollection()
            ->addFieldToFilter('date_time', array('from' => $lastTime, 'to' => $time))
            ->addFieldToFilter('status', 1);
        $allCount = $allCollection->count();
        $allCollection->clear();
        $currentUserCollection = $allCollection
            ->addFieldToFilter('country_id', substr($userData['country_id'], 0, 3));
        $currentUserCollection->getSelectCountSql();
        $currentUserCount = $currentUserCollection->count();

        if (($allCount >= Amasty_Audit_Model_Data::MIN_ALL_COUNT) && ($currentUserCount == 0)) {
            return true;
        }

        return false;
    }

    public function logout($userData)
    {
        $user = Mage::getModel('admin/user')->loadByUsername($userData['username']);
        $userData['name'] = $user->getFirstname() . ' ' . $user->getLastname();
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $userData['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $userData['ip'] = $_SERVER['REMOTE_ADDR'];
        }
//        $userData['ip'] = '87.252.238.217';//Minsk
//        $userData['ip'] = '72.229.28.185';//NY
        $userData['status'] = Amasty_Audit_Model_Data::LOGOUT;
        if (Mage::helper('core')->isModuleEnabled('Amasty_Geoip') && (Mage::getStoreConfig('amaudit/geoip/use') == 1) && !is_null($userData['ip'])) {
            $geolocationModel = Mage::getSingleton('amaudit/geolocation');
            $location = $geolocationModel->getLocation($userData['ip']);
            $userData['location'] = $location['locationString'];
            $userData['country_id'] = $location['countryLabel'];
        }
        $this->setData($userData);
        $this->save();
    }
}