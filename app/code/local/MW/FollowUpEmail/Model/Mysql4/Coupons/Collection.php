<?php

class MW_FollowUpEmail_Model_Mysql4_Coupons_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 

{

    public function _construct() 

    {

        parent::_construct();

        $this->_init('followupemail/coupons');

    }

	

	public function addFieldToFilter($attribute, $condition=null)

    {		

    	if($attribute=='code' || $attribute=='coupon_status') $attribute = 'main_table.'.$attribute;

    	return parent::addFieldToFilter($attribute, $condition);

    }

}