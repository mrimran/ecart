<?php



class MW_FollowUpEmail_Helper_Coupon extends Mage_Core_Helper_Abstract

{

    const MYSQL_DATETIME_FORMAT = 'Y-m-d';



    public function generateCode($rule,$email = '')

    {

		if($rule != null){			

			if($rule['coupon_status'] == 1){				

				return $this->saveCoupon($rule,$email);			

			}

		}

    	

    }



    public function saveCoupon($rule,$email = '')

    {		

		$coupon = Mage::getModel('followupemail/coupons');

		

        $day = ((int)$rule['coupon_expire_days'] * 24) * 3600;

		

        $expires = date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT, $day + time()); 

		

        $createdate = date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT, time());

		

        $uniqueCode = $rule['coupon_prefix'] . dechex($rule['rule_id']) . 'X' . strtoupper(uniqid());                                    

		

		$coupon->setCouponId(null)

				->setRuleId($rule['rule_id'])

               ->setSaleRuleId($rule['coupon_sales_rule_id'])

			   ->setCode($uniqueCode)

			   ->setUseCustomer($email)

			   ->setTimesUsed(0)

               ->setExpirationDate(null)               

               ->setCreatedAt(null)               

               ->setCouponStatus(MW_FollowUpEmail_Model_System_Config_Statuscoupon::COUPON_STATUS_PENDING);

		$coupon->save();        

       return $uniqueCode;

    }



}

