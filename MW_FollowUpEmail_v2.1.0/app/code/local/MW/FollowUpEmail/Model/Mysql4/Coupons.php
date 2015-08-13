<?php
class MW_FollowUpEmail_Model_Mysql4_Coupons extends Mage_Core_Model_Mysql4_Abstract
{
    const MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';

    public function _construct() 
    {
       $this->_init('followupemail/coupons', 'coupon_id');
    }   	
}