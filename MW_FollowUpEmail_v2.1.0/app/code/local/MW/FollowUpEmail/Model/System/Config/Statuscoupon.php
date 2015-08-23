<?php

class MW_FollowUpEmail_Model_System_Config_Statuscoupon

{

    const COUPON_STATUS_PENDING         = 1;

    const COUPON_STATUS_SENT            = 2;
	
    const COUPON_STATUS_USED            = 3;
	
    const COUPON_STATUS_EXPIRED         = 4;



    public static function toOptionArray()

    {        

        return array(

            self::COUPON_STATUS_PENDING  => 'Pending',

            self::COUPON_STATUS_SENT   => 'Sent',        
			    
            self::COUPON_STATUS_USED   => 'Used',            
			
            self::COUPON_STATUS_EXPIRED   => 'Expired',            

        );

    }

}