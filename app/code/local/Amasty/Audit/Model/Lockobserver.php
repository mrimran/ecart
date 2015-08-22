<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_Model_Lockobserver
{
    //listen admin_session_user_login_success event
    public function onAdminSessionUserLoginSuccess($observer)
    {
        $lockModel = Mage::getSingleton('amaudit/lock');
        $mailsendModel = Mage::getSingleton('amaudit/mailsender');
        $dataModel = Mage::getSingleton('amaudit/data');
        $user = Mage::getModel('admin/user')->loadByUsername($observer->getUser()->getUsername());
        $userData = $this->_prepareUserData($user);
        $userData['status'] = Amasty_Audit_Model_Data::SUCCESS;
        $isUserLock = false;

        if (Mage::getStoreConfig('amaudit/login/enableLock')) {
            $isUserLock = $lockModel->isUserLocked($user->getUserId());
        }

        if (!$isUserLock) {
            $successfulMail = Mage::getStoreConfig('amaudit/log_mailing/send_to_mail');
            if ((Mage::getStoreConfig('amaudit/log_mailing/enabled') != 0) &&
                !empty($successfulMail)
            ) {
                $mailsendModel->sendMail($userData, 'success', $successfulMail);
            }

            $suspiciousMail = Mage::getStoreConfig('amaudit/suspicious_log_mailing/send_to_mail');
            if (((Mage::getStoreConfig('amaudit/suspicious_log_mailing/enabled') != 0) &&
                !empty($suspiciousMail) && Mage::getStoreConfig('amaudit/geoip/use'))
            ) {
                $isSuspicious = $dataModel->isSuspicious($userData);
                if ($isSuspicious){
                    $mailsendModel->sendMail($userData, 'suspicious', $suspiciousMail);
                }
            }
        } else {
            $userData['status'] = Amasty_Audit_Model_Data::LOCKED;
        }

        $this->_saveAudit($userData);
        if ($userData['status'] != Amasty_Audit_Model_Data::LOCKED) {
            $activeModel = Mage::getModel('amaudit/active');
            $activeModel->saveActive($userData);

            $visitModel = Mage::getModel('amaudit/visit');
            $visitModel->startVisit($userData);
        }
    }

    //listen admin_session_user_login_failed event 
    public function onAdminSessionUserLoginFailed($observer)
    {
        $dataModel = Mage::getSingleton('amaudit/data');
        $mailsendModel = Mage::getSingleton('amaudit/mailsender');
        Mage::app()->getResponse()->setHttpResponseCode(403);
        $user = Mage::getModel('admin/user')->loadByUsername($observer->getUserName());
        $userIsFound = false;
        if ($user->hasData('username')) {
            $userIsFound = true;
        } else {
            $user = $observer;
        }
        $userData = $this->_prepareUserData($user, $userIsFound);
        $userData['status'] = Amasty_Audit_Model_Data::UNSUCCESS;

        $receiveUnsuccessfulEmail = Mage::getStoreConfig('amaudit/unsuccessful_log_mailing/send_to_mail');
        if ((Mage::getStoreConfig('amaudit/unsuccessful_log_mailing/enabled') != 0) &&
            !empty($receiveUnsuccessfulEmail)
        ) {
            $unsuccessfulCount = $dataModel->getUnsuccessfulCount() + 1;//"+1" Because saving failed login is later
            if ($unsuccessfulCount >= Amasty_Audit_Model_Data::MIN_UNSUCCESSFUL_COUNT) {
                $userData['unsuccessful_login_count'] = $unsuccessfulCount;
                $mailsendModel->sendMail($userData, 'unsuccessful', $receiveUnsuccessfulEmail);
            }
        }

        if (Mage::getStoreConfig('amaudit/login/enableLock')) {
            $user = Mage::getModel('admin/user')->loadByUsername($observer->getUserName());
            if ($user && 0 != $user->getUserId()) {
                $this->_saveLock($user->getUserId());
            }
        }
        $this->_saveAudit($userData);
    }

    protected function _prepareUserData($user, $userIsFound = true)
    {
        $userData = array();
        $userData['date_time'] = Mage::getModel('core/date')->gmtDate();
        if ($userIsFound) {
            $userData['username'] = $user->getUsername();
            $userData['name'] = $user->getFirstname() . ' ' . $user->getLastname();
        } else {
            $userData['username'] = $user->getUserName();
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $userData['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $userData['ip'] = $_SERVER['REMOTE_ADDR'];
        }
//        $userData['ip'] = '72.229.28.185';//NY
//        $userData['ip'] = '87.252.238.217';//Minsk

        if (Mage::helper('core')->isModuleEnabled('Amasty_Geoip') && (Mage::getStoreConfig('amaudit/geoip/use') == 1) && !is_null($userData['ip'])) {
            $geolocationModel = Mage::getSingleton('amaudit/geolocation');
            $location = $geolocationModel->getLocation($userData['ip']);
            $userData['location'] = $location['locationString'];
            $userData['country_id'] = $location['countryLabel'];
        }

        return $userData;
    }

    private function _saveAudit($userData)
    {
        $dataModel = null;
        try {
            $dataModel = Mage::getModel('amaudit/data');
            $dataModel->setData($userData);
            $dataModel->save();
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::log($e->getMessage());
        }
    }

    private function _saveLock($userId)
    {
        $lockModel = Mage::getModel('amaudit/lock');
        $count = 0;
        $user = Mage::getModel('amaudit/lock')->load($userId, 'user_id');
        if ($user->hasData()) {
            $count = $user->getCount();
            if ($count >= Mage::getStoreConfig('amaudit/login/numberFailed') - 1) {
                $user->setData('time_lock', time());
            }
            $count++;
            $count = $count > Mage::getStoreConfig('amaudit/login/numberFailed') ? Mage::getStoreConfig('amaudit/login/numberFailed') : $count;
            $user->setData('count', $count);
            $user->save();
        } else {
            $count++;
            $lockModel->setData('user_id', $userId);
            $lockModel->setData('count', $count);
            $lockModel->save();
        }

    }
}