<?php

/**
 * Description of Observer
 *
 * @author imran
 */
class WTS_DeleteCustomerSyncOneAll_Model_Observer
{

    public function deleteCustSyncOneAll($observer)
    {
        $custId = $observer->getCustomer()->getId();
        Mage::log("+++"."Now deleting the customer entry (#$custId) for social login in WTS_DeleteCustomerSyncOneAll_Model_Observer.");

        //delete user from social login as well.
        $model = Mage::getModel('oneall_sociallogin/entity');
        try {
            $model->setId($custId)->delete();
        } catch (Exception $e) {
            //throw Mage::throwException($e->getMessage());//no need to show any message, just log it.
            Mage::logException("ERROR:".$e->getMessage()." in WTS_DeleteCustomerSyncOneAll_Model_Observer for cust_id:".$custId);
        }
    }

}
