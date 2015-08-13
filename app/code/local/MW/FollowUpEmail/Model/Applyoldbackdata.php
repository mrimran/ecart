<?php

class MW_FollowUpEmail_Model_Applyoldbackdata

{

  	public function Eventoldback($data){

		$rule = $data->getData();

		$event = $rule['event'];		

		$findOrder   = 'order';

		$findCart   = 'cart';

		$findCustomer   = 'customer';

		$posOrder = strpos($event, $findOrder);

		if($posOrder !== false){			

			$this->Oldbackdataorder($rule);

		}

		

		$posCart = strpos($event, $findCart);

		if($posCart !== false){			

			$this->Oldbackdatacart($rule);

		}

		return true;

	}

	

	public function Oldbackdataorder($rule){

		//grab orders in the past $day_old_back days.

		//old back data

		$day_old_back = 0;	

		$emailChain = unserialize($rule['email_chain']);				

		foreach($emailChain as $emailChainItem){				

			if($day_old_back < $emailChainItem['DAYS']) $day_old_back = $emailChainItem['DAYS'];					

		}

		$status = "";

		if($rule['event'] == "new_order_placed") $status = "pending";

		if($rule['event'] == "order_status_processing") $status = "processing";

		if($rule['event'] == "order_status_complete") $status = "complete";

		if($rule['event'] == "order_status_closed") $status = "closed";

		if($rule['event'] == "order_status_canceled") $status = "canceled";

		if($rule['event'] == "order_updated"){

			$arrCondition = unserialize($rule["conditions_serialized"]);

			foreach($arrCondition['conditions'] as $condition){

				if($condition['attribute'] == "order_status") $rule['event'] = $condition['value'];

			}

		}

		if($rule['event'] == "order_status_canceled") $status = "canceled";

		if($rule['event'] == "order_status_closed") $status = "closed";

		if($rule['event'] == "order_status_complete") $status = "complete";

		if($rule['event'] == "order_status_fraud") $status = "fraud";

		if($rule['event'] == "order_status_holded") $status = "holded";

		if($rule['event'] == "order_status_payment_review") $status = "payment_review";

		if($rule['event'] == "order_status_pending") $status = "pending";

		if($rule['event'] == "order_status_pending_payment") $status = "pending_payment";

		if($rule['event'] == "order_status_pending_paypal") $status = "pending_paypal";

		if($rule['event'] == "order_status_processing") $status = "processing";

		$lastday = date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT, time()-($day_old_back*86400));

		$now = time()-($day_old_back*86400);

		

		//order in emailqueue

		$orderExistEmailQueue = Mage::getModel('followupemail/emailqueue')->getCollection()->getColumnValues("order_id");

		

		if($orderExistEmailQueue != null){		

			$orders = Mage::getModel('sales/order')->getCollection()

			    ->addAttributeToFilter('updated_at', array('from'  => $lastday))

				->addAttributeToFilter('updated_at', array('to'  => date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT, time()-60)))

			    ->addAttributeToFilter('status', array('eq' => $status))

			    ->addAttributeToFilter('entity_id', array('nin' => $orderExistEmailQueue));					

		}

		else{

			$orders = Mage::getModel('sales/order')->getCollection()

			    ->addAttributeToFilter('updated_at', array('from'  => $lastday))

				->addAttributeToFilter('updated_at', array('to'  => date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT, time()-60)))

			    ->addAttributeToFilter('status', array('eq' => $status));

		}

		

		if(count($orders)>0){

			foreach($orders as $order){

				if(!Mage::getModel('followupemail/validate')->validate(unserialize($rule["conditions_serialized"]),$order,null,$order->getCustomerId())) continue;

				$storeId = $order->getStoreId();

				$store = Mage::getModel('core/store')->load($storeId);

				$queue = Mage::getModel('followupemail/emailqueue');

				$items = $order->getAllItems();



				$productIds = array();



				$senderInfo = array();



				$senderInfo['sender_name'] = $rule['sender_name'];



				$senderInfo['sender_email'] = $rule['sender_email'];					



				foreach($items as $item){

					

					if ($item->getParentItem()) continue;

					

					$productIds[] = $item->getProductId();



				}	

					

				$customerInfo = Mage::getModel('followupemail/observer')->_getCustomer($order->getCustomerId(),$order);

								

				foreach ($emailChain as $emailChainItem) {

				$params = array();

				

            	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);				



				$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];
				

				$code = MW_FollowUpEmail_Helper_Data::encryptCode($customerInfo['customer_email'],'order',$order->getId());



				$linkDirect = $store->getUrl('followupemail/index/direct', array('code' => $code));				



				$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];



				$params['senderInfo'] = $senderInfo;



				$params['productIds'] = $productIds;



				$params['orderId'] = $order->getId();						



				$params['data'] = "";



				$params['customer'] = "";



				$params['customerId'] = $order->getCustomerId();



				$params['cart'] = "";



				$params['storeId'] = $storeId;



				$params['code'] = $code;



				$content = "";



				if($customerInfo['customer_email'] == "") continue;
				$intTimeSent = strtotime($order->getUpdatedAt()) + $timeSent * 60;
				if($intTimeSent < time()) continue;
		
				/*mage::log("Curent time");
				mage::log(date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,time()));
				mage::log("Time sent");
				mage::log(date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,$intTimeSent));*/
				/*if(Mage::getModel('followupemail/observer')->_checkExistQueueEmail($rule['rule_id'],$order->getId(),$customerInfo['customer_email'],$emailTemplate['code'],strtotime($order->getUpdatedAt()) + $timeSent * 60,0)){	*/					

						$coupon = Mage::helper('followupemail/coupon')->generateCode($rule,$customerInfo['customer_email']);

						$params['coupon'] = $coupon;

						$queue->add(



	                        strtotime($order->getUpdatedAt()) + $timeSent * 60,



							$rule['rule_id'],



							$order->getId(),



							$emailTemplate['sender_name'],



							$emailTemplate['sender_email'],



							$customerInfo['customer_name'],				



							$customerInfo['customer_email'],



							$emailTemplate['subject'],				



							$content,



							serialize($params),



							$emailTemplate['code'],



							0,



							$code,

							"",

							$coupon



                    	);

					//}

					

				}

				//end foreach emailchain				

			}

			// end foreach orders

		}

		//end if count order > 0

	}

	

	public function Oldbackdatacart($rule){	

	

		//grab orders in the past $day_old_back days.

		//old back data

		$day_old_back = 0;	

		$emailChain = unserialize($rule['email_chain']);					

		foreach($emailChain as $emailChainItem){				

			if($day_old_back < $emailChainItem['DAYS']) $day_old_back = $emailChainItem['DAYS'];					

		}

		

		//order in emailqueue

		//$emailExistEmailQueue = Mage::getModel('followupemail/emailqueue')->getCollection()->getColumnValues("recipient_email");

			//->addAttributeToFilter('is_abandoncart', array('eq' => 1))

		$emailExistEmailQueue = Mage::getModel('followupemail/emailqueue')->getCollection()->addFieldToFilter('is_abandoncart', array('eq' => 1))->getColumnValues("recipient_email");	

				

		$product = Mage::getModel('catalog/product');



        $resource = Mage::getSingleton('core/resource');



        $read = $resource->getConnection('core_read');

		

        $select = $read->select()



            ->from(array('q' => $resource->getTableName('sales/quote')), array(



            'store_id' => 'q.store_id',



            'quote_id' => 'q.entity_id',



            'customer_id' => 'q.customer_id',



            'subtotal' => 'q.subtotal',



            'subtotal_with_discount' => 'q.subtotal_with_discount',



            'grand_total' => 'q.grand_total',



            'items_qty' => 'q.items_qty',



            //'store_id' => 'q.store_id',



            'updated_at' => 'q.updated_at'))



            ->joinLeft(array('a' => $resource->getTableName('sales/quote_address')),



            'q.entity_id=a.quote_id AND a.address_type="billing"',



            array(



                'customer_email' => new Zend_Db_Expr('IFNULL(q.customer_email, a.email)'),



                'customer_firstname' => new Zend_Db_Expr('IFNULL(q.customer_firstname, a.firstname)'),



                'customer_middlename' => new Zend_Db_Expr('IFNULL(q.customer_middlename, a.middlename)'),



                'customer_lastname' => new Zend_Db_Expr('IFNULL(q.customer_lastname, a.lastname)'),



				'city' => 'a.city',



                'state' => 'a.region',



                'zipcode' => 'a.postcode',



                'country_id' => 'a.country_id',



            ))



            ->joinInner(array('i' => $resource->getTableName('sales/quote_item')), 'q.entity_id=i.quote_id', array(



            'product_ids' => new Zend_Db_Expr('GROUP_CONCAT(i.product_id)'),



            'item_ids' => new Zend_Db_Expr('GROUP_CONCAT(i.item_id)'),



			'sku' => new Zend_Db_Expr('GROUP_CONCAT(i.sku)'),



            'product_type' => new Zend_Db_Expr('GROUP_CONCAT(i.product_type)')



        	))



            ->where('q.is_active=1') 



			->where('q.updated_at > ?', date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,



            time() - ($day_old_back*86400)))	



            ->where('q.items_count>0')



            ->where('q.customer_email IS NOT NULL OR a.email IS NOT NULL')



            ->where('i.parent_item_id IS NULL')

          //  ->where('q.customer_email not in ?',$emailExistEmailQueue)

			->where('q.customer_email not in (?)', $emailExistEmailQueue)



            ->group('q.entity_id')



            ->order('updated_at')	;

			//->addAttributeToFilter('q.customer_email', array('nin' => $emailExistEmailQueue));	

			

				



		//mage::log(date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,$now - ($intFromTimeHour+$intTimeLastHour)));

        $carts = $read->fetchAll($select);

				

		$storeId = Mage::app()->getStore()->getStoreId();		



		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

		



		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		



        $rules = $rulecollection->loadRulesByEvent(MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::EVENT_TYPE_ABANDON_CART,$storeId,$groupId)->getData();			



		foreach ($carts as $cart) {				

	

			/*$timeAbandonCart = strtotime($cart['updated_at'])+$this->_intTimeLast;



			if($timeAbandonCart > time()) continue;	*/		



			$store = Mage::getModel('core/store')->load($storeId);



            $productIds = explode(',', $cart['product_ids']);   



			$customerInfo = Mage::getModel('followupemail/observer')->_getCustomer($cart['customer_id'],null);         



			foreach($rules as $rule){						



				if(!Mage::getModel('followupemail/validate')->validate(unserialize($rule["conditions_serialized"]),null,$cart,$cart['customer_id'])) continue;



				$senderInfo = array();



				$senderInfo['sender_email'] = $rule['sender_email'];



				$senderInfo['sender_name'] = $rule['sender_name'];



				$emailChain = unserialize($rule['email_chain']);



				$queue = Mage::getModel('followupemail/emailqueue');								



				foreach ($emailChain as $emailChainItem) {					



					//get content of current email template							



                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);					



					//$emailTemplateContent = $emailTemplate['content'];  



					$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];	



					$code = MW_FollowUpEmail_Helper_Data::encryptCode($cart['customer_email'],'cart',0);



					$linkDirect = $store->getUrl('followupemail/index/direct', array('code' => $code));					



					$params = array();



					$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];



					$params['senderInfo'] = $senderInfo;



					$params['productIds'] = $productIds;



					$params['orderId'] = "";



					$params['cart'] = $cart;					



					$params['data'] = "";



					$params['customer'] = "";	



					$params['customerId'] = $cart['customer_id'];



					$params['storeId'] = $storeId;



					$params['code'] = "";				

					$params['codeCart'] = $code;				



					//$content = $this->_prepareContentEmailAbandonCart($emailTemplate,$cart,$customerInfo,$linkDirect,$productIds);



					$content = "";



					// $scheduledAt, $sentAt, $ruleId, $orderId, $senderName, $recipientName, $recipientEmail, $subject, $content, $params

					if($cart['customer_email'] == "") continue;

					//if(Mage::getModel('followupemail/observer')->_checkExistQueueEmail($rule['rule_id'],0,$cart['customer_email'],$emailTemplate['code'],strtotime($cart['updated_at']) + $timeSent * 60,1)){



							$coupon = Mage::helper('followupemail/coupon')->generateCode($rule,$cart['customer_email']);

							$params['coupon'] = $coupon;

							$queue->add(



		                        strtotime($cart['updated_at']) + $timeSent * 60,



								$rule['rule_id'],



								0,



								$emailTemplate['sender_name'],



								$emailTemplate['sender_email'],



								$cart['customer_firstname'].' '.$cart['customer_lastname'],				



								$cart['customer_email'],



								$emailTemplate['subject'],				



								$content,



								serialize($params),



								$emailTemplate['code'],



								1,



								$code,

								"",

								$coupon



	                    	);	



						//}



					//}                   			               



				}



			}			



		}	

	}

	

	public function Oldbackdatacustomer($data){

		

	}

}