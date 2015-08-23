<?php

class MW_FollowUpEmail_Model_Coupons extends Mage_Core_Model_Abstract {
	
	 /*

     * Class constructor

     */
	
    public function _construct()

    {

		parent::_construct();

        $this->_init('followupemail/coupons');

    }  
	
	 public function getByCode($code) {        
        
        $coupon = $this->getCollection();
		        
        $coupon->getSelect()->where("code LIKE ?", $code);            
		
		foreach($coupon as $c){
			return $c;
		}
		      
        return null;
    }

}
