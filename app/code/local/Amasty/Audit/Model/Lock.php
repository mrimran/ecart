<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_Model_Lock extends Mage_Core_Model_Abstract
{
    public function _construct()
    {    
        $this->_init('amaudit/lock', 'entity_id');
    }

    public function isUserLocked($userId)
    {
        $lockStatus = false;
        $userLock = Mage::helper('amaudit')->getLockUser($userId);

        if($userLock){
            $time = intval($userLock->getTimeLock());
            if($userLock->getCount() >= Mage::getStoreConfig('amaudit/login/numberFailed') && $time){
                $locktime = time() - $time;
                if( $locktime <= Mage::getStoreConfig('amaudit/login/time') || Mage::getStoreConfig('amaudit/login/time') == 0 || Mage::getStoreConfig('amaudit/login/time') == ""){
                    Mage::getSingleton('admin/session')->unsetAll();
                    Mage::getSingleton('adminhtml/session')->unsetAll();
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Invalid Username or Password.'));
                    $lockStatus = true;
                }
            }
            if(!$lockStatus){
                try
                {
                    $userLock->setData('count', 0);
                    $userLock->setData('time_lock', null);
                    $userLock->save();
                }
                catch (Exception $e)
                {
                    Mage::logException($e);
                    Mage::log($e->getMessage());
                }
            }
        }

        return $lockStatus;
    }
    
}