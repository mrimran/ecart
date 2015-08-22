<?php

    class MW_FollowUpEmail_Model_System_Config_EventFollowUpEmail extends Mage_Core_Model_Abstract

    {		
		
		
		const SPACE         			= '""';
		
		const ORDER_STATUS_PREFIX         			= 'order_status_';

    	const EVENT_TYPE_ABANDON_CART               = 'abandoned_cart_appeared';
		
    	const EVENT_TYPE_ABANDON_CART_FOR_GUEST              = 'abandoned_cart_appeared_for_guest';

    	const NEW_ORDER_PLACED          			= 'new_order_placed';

    	const ORDER_UPDATED      					= 'order_updated';

    	const ORDER_COMPLETED           			= 'order_status_complete';

    	const ORDER_CANCELLED  						= 'order_status_canceled';

    	const ORDER_CLOSED             				= 'order_status_closed';

    	const ORDER_PROCESSING             			= 'order_status_processing';

    	const NEW_CUSTOMER_SIGNED_UP        		= 'new_customer_signed_up';

    	const CUSTOMER_LOGGED_IN        			= 'customer_logged_in';

    	const CUSTOMER_ACCOUNT_UPDATED        		= 'customer_account_updated';

	    const CUSTOMER_SUBSCRIBED_NEWLETTER         = 'customer_subscribed_newsletter';

	    const CUSTOMER_UNSUBSCRIBED_NEWLETTER       = 'customer_unsubscribed_newsletter';

	    const FREE_TRIAL_DOWNLOAD       			= 'free_trial_download';

	    const FREE_TRIAL_REQUEST       				= 'free_trial_request';

	    const FREE_TRIAL_REMIND       				= 'free_trial_remind';

	    const FREE_DOWNLOAD       					= 'free_download';

	    const EVENT_TYPE_CART_UPDATED       		= 'cart_updated';
		
		const CUSTOMER_BIRTHDAY       				= 'customer_birthday';

	    

        public static function toOptionArray($display = true)

        {

			$options = self::toShortOptionArray($display);

	        $values = array();



	        foreach($options as $k => $v)

	            $values[] = array(

	                'value' => $k,

	                'label' => $v

	            );



	        return $values;

        }

		

		public static function toShortOptionArray($display = true)

	    {

			$result = array();

			if($display){
				$result[self::SPACE]          = Mage::helper('followupemail')->__('');				
				$result[self::EVENT_TYPE_CART_UPDATED]          = Mage::helper('followupemail')->__('Cart Updated');				
			}
	        

			else{
				$result[self::EVENT_TYPE_ABANDON_CART]          = Mage::helper('followupemail')->__('Abandoned Cart');
			
			$result[self::EVENT_TYPE_ABANDON_CART_FOR_GUEST]          = Mage::helper('followupemail')->__('Abandoned Cart For Guest');				
			}
			

	        $result[self::NEW_ORDER_PLACED]  				= Mage::helper('followupemail')->__('New Order Placed');
			
			$result[self::ORDER_PROCESSING]     			= Mage::helper('followupemail')->__('Order Processing');
			
			$result[self::ORDER_COMPLETED]     				= Mage::helper('followupemail')->__('Order Completed');	

	        $result[self::ORDER_UPDATED]         			= Mage::helper('followupemail')->__('Order Updated');
			
			$result[self::ORDER_CLOSED]     				= Mage::helper('followupemail')->__('Order Closed');	            

	        $result[self::ORDER_CANCELLED] 					= Mage::helper('followupemail')->__('Order Cancelled');	        
			if(!$display)
			$result[self::CUSTOMER_BIRTHDAY] 				= Mage::helper('followupemail')->__('Customer Birthday');	        
			

			if($display)

			$result[self::CUSTOMER_LOGGED_IN]           	= Mage::helper('followupemail')->__('Customer Logged In');

	        $result[self::NEW_CUSTOMER_SIGNED_UP]           = Mage::helper('followupemail')->__('New Customer Signed Up');

	        $result[self::CUSTOMER_ACCOUNT_UPDATED]         = Mage::helper('followupemail')->__('Customer Account Updated');

	       /* $result[self::CUSTOMER_SUBSCRIBED_NEWLETTER]    = Mage::helper('followupemail')->__('Customer Subscribed Newsletter');			

	        $result[self::CUSTOMER_UNSUBSCRIBED_NEWLETTER]  = Mage::helper('followupemail')->__('Customer Unsubscribed Newsletter');

	        $result[self::FREE_TRIAL_DOWNLOAD]  			= Mage::helper('followupemail')->__('Free Trial Download');

	        $result[self::FREE_TRIAL_REQUEST]  				= Mage::helper('followupemail')->__('Free Trial Request');

	        $result[self::FREE_TRIAL_REMIND]  				= Mage::helper('followupemail')->__('Free Trial Reminder');

	        $result[self::FREE_DOWNLOAD]  					= Mage::helper('followupemail')->__('Free Download');*/

			

			return $result;

	    }

		

		public static function getOrderStatus(){

			$values = array();

			$orderStatuses = Mage::getSingleton('sales/order_config')->getStatuses();

        	foreach ($orderStatuses as $code => $name)

            	$values[self::ORDER_STATUS_PREFIX . $code] = Mage::helper('followupemail')->__("'%s' status", $name);

			

			return $values;

		}		

    }

?>