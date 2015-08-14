<?php

class MW_FollowUpEmail_Model_Observer

{

	protected $_dontsendemailtime;

	protected $_intTimeLast;

	protected $_intTimeFrom;

	protected $_now;
	
	protected $_intTimeCleanMail;

	

	public function _intTime()

    {

        $cartisabandonedafterconfig = Mage::getStoreConfig('followupemail/config/cartisabandonedafter');	

		$dontsendemailhoursconfig = Mage::getStoreConfig('followupemail/config/dontsendemailhours');	
		
		$autocleanqueueemail = Mage::getStoreConfig('followupemail/config/autocleanqueueemail');	

		if(is_numeric($cartisabandonedafterconfig)){

			$this->_intTimeLast = ($cartisabandonedafterconfig * 60)*60;	

		}

		else

			$this->_intTimeLast = 24 * 3600;	

		

		if(is_numeric($dontsendemailhoursconfig)){

			$this->_dontsendemailtime = ($dontsendemailhoursconfig * 60)*60;	

		}

		else

			$this->_dontsendemailtime = 24 * 3600;	
			
		if(is_numeric($autocleanqueueemail)){

			$this->_intTimeCleanMail = ($autocleanqueueemail * 24)*3600;	

		}

		else

			$this->_intTimeCleanMail = (60 * 24) * 3600;	

						

		$this->_intTimeFrom = 24 * 3600;	

		$this->_now = time();			

    }

	

	public function getCheckoutSession()

    {

        return Mage::getSingleton('checkout/session');

    }

	

	public function eventAddQueue($arvgs){
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		$this->_intTime();

		$order = $arvgs->getOrder();

		$groupId = $order->getCustomerGroupId();

		$storeId = $order->getStoreId();

		$store = Mage::getModel('core/store')->load($storeId);

		$this->eventDeleteQueue($arvgs);

		$eventStatus = MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::ORDER_STATUS_PREFIX.$order->getStatus();		                    
		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();

        $rules = $rulecollection->loadRulesByEvent($eventStatus,$storeId,$groupId)->getData();
                 
		if(is_array($rules) && count($rules) > 0){

			foreach($rules as $rule){
					
				if($eventStatus == $rule['event']){

					// old back data
					
					$day_old_back = 0;
					
					if($rule["rules_apply_old_back"] == 1){
					
						$emailChain = unserialize($rule['email_chain']);
						
						foreach($emailChain as $emailChainItem){							

							if($day_old_back < $emailChainItem['DAYS']) $day_old_back = $emailChainItem['DAYS'];
							
						}
					}
					$items = $order->getAllItems();					

					$productIds = array();
					
					$senderInfo = array();

					$senderInfo['sender_name'] = $rule['sender_name'];

					$senderInfo['sender_email'] = $rule['sender_email'];					

					foreach($items as $item){
						
						if ($item->getParentItem()) continue;
						
						$productIds[] = $item->getProductId();

					}

					//$params[] = $order->getId();

					$emailChain = unserialize($rule['email_chain']);		           

		            $queue = Mage::getModel('followupemail/emailqueue');		          
												
					if(!Mage::getModel('followupemail/validate')->validate(unserialize($rule["conditions_serialized"]),$order,null,$order->getCustomerId())) continue;	
					
					$customerInfo = $this->_getCustomer($order->getCustomerId(),$order);					

					foreach ($emailChain as $emailChainItem) {

						$params = array();

						//get content of current email template							

	                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

						//Mage::log($emailTemplate);

						//$emailTemplateContent = $emailTemplate['content'];  						

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

						//$content = $this->_prepareContentEmail($emailTemplate,$productIds,$order,$customerInfo,$linkDirect);

						$content = "";

						// $scheduledAt, $sentAt, $ruleId, $orderId, $senderName, $recipientName, $recipientEmail, $subject, $content, $params
						if($customerInfo['customer_email'] == "") continue;
						if($this->_checkExistQueueEmail($rule['rule_id'],$order->getId(),$customerInfo['customer_email'],$emailTemplate['code'],0)){							
								$coupon = Mage::helper('followupemail/coupon')->generateCode($rule,$customerInfo['customer_email']);
								$params['coupon'] = $coupon;
								$queue->add(

			                        time() + $timeSent * 60,

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
						}	                   			               

					}					
				}				
				// end if event status
			}		

		}

		// Check event type is Order Updated

		$ruleCollectionOrderUpdated = Mage::getModel('followupemail/rules')->getCollection();

		$rulesOrderUpdated = $ruleCollectionOrderUpdated->loadRulesByEvent(MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::ORDER_UPDATED,$storeId,$groupId)->getData();		

		if(is_array($rulesOrderUpdated) && count($rulesOrderUpdated) > 0){			

			foreach($rulesOrderUpdated as $rule){	

				$senderInfo = array();

				$senderInfo['sender_name'] = $rule['sender_name'];

				$senderInfo['sender_email'] = $rule['sender_email'];							

				$conditionsArr = unserialize($rule['conditions_serialized']);

				$collectionCondition = $conditionsArr['conditions'];

				foreach($collectionCondition as $condition){

					$eventCondition = $condition['value'];					

					if($eventStatus == $eventCondition){
						
						if(!Mage::getModel('followupemail/validate')->validate(unserialize($rule["conditions_serialized"]),$order,null,$order->getCustomerId())) continue;	

						$productIds = array();						
						$items = $order->getAllItems();	
						foreach($items as $item){
 						if ($item->getParentItem()) continue;
						$productIds[] = $item->getProductId();
						}
						
						$emailChain = unserialize($rule['email_chain']);		           

			            $queue = Mage::getModel('followupemail/emailqueue');		          

						$customerInfo = $this->_getCustomer($order->getCustomerId(),$order);					

						foreach ($emailChain as $emailChainItem) {

							$params = array();							

							//get content of current email template							

		                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

							//Mage::log($emailTemplate);

							//$emailTemplateContent = $emailTemplate['content'];  

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

							//$content = $this->_prepareContentEmail($emailTemplate,$productIds,$order,$customerInfo,$linkDirect);

							$content = "";
							if($customerInfo['customer_email'] == "") continue;
							if($this->_checkExistQueueEmail($rule['rule_id'],$order->getId(),$customerInfo['customer_email'],$emailTemplate['code'],0)){

									$coupon = Mage::helper('followupemail/coupon')->generateCode($rule,$customerInfo['customer_email']);
									$params['coupon'] = $coupon;
									$queue->add(

				                        time() + $timeSent * 60,

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

						}

					}//end

				}

			}	

		}		

	}

	

	public function newOrder($arvgs){		
		$config = Mage::getStoreConfig('followupemail/config/enabled');		
		if(!$config) return false;
		
		$this->_intTime();

		$order = $arvgs->getOrder();
		
		// Disable coupon
        Mage::getSingleton('core/session')->unsEmailGuest();
		$coupon = Mage::getModel('followupemail/coupons');
		
		$couponData = $coupon->getByCode($order->getCouponCode());
		if($couponData != null){
			$couponId = $couponData->getCouponId();
		
			$coupon->load($couponId)

        	->setCouponStatus(MW_FollowUpEmail_Model_System_Config_Statuscoupon::COUPON_STATUS_USED)       	

        	->save();	
		}	
		
		$queueId = Mage::getSingleton('core/session')->getQueueId();
		if($queueId != ""){			
			$model = Mage::getModel('followupemail/emailqueue');			
			$model->load($queueId);
			$model->setCustomerResponse(MW_FollowUpEmail_Model_System_Config_Response::QUEUE_STATUS_PURCHASED);
			$model->save();	
			$queueId = Mage::getSingleton('core/session')->setQueueId("");
		}
		$items = $order->getAllItems();

		$productIds = array();

		$sku = array();

		foreach($items as $item){
			if ($item->getParentItem()) continue;
			$productIds[] = $item->getProductId();

			$sku[] = $item->getSku();

		}

		$customer = $this->_getCustomer($order->getCustomerId(),$order);		

		$groupId = $order->getCustomerGroupId();

		$storeId = $order->getStoreId();

		$store = Mage::getModel('core/store')->load($storeId);	
        
        //fix
        $eventStatus1 = MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::ORDER_STATUS_PREFIX.$order->getStatus();        
       
		$eventStatus = MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::NEW_ORDER_PLACED;               
                
        $this->eventDeleteQueueSpecial($eventStatus,$order->getCustomerEmail(),$sku,0,$storeId,1);       
		$this->eventDeleteQueueSpecial($eventStatus,$customer['customer_email'],$sku,$groupId,$storeId,0);

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();

        $rules = $rulecollection->loadRulesByEvent($eventStatus,$storeId,$groupId)->getData();		
       
		if(is_array($rules) && count($rules) > 0){

			foreach($rules as $rule){

				if($eventStatus == $rule['event']){	
					
					$senderInfo = array();

					$senderInfo['sender_name'] = $rule['sender_name'];

					$senderInfo['sender_email'] = $rule['sender_email'];											

					$cartInfo = array();									

					//$params[] = $order->getId();

					$emailChain = unserialize($rule['email_chain']);		           

		            $queue = Mage::getModel('followupemail/emailqueue');
														
					if(!Mage::getModel('followupemail/validate')->validate(unserialize($rule["conditions_serialized"]),$order,null,$order->getCustomerId())) continue;							

					foreach ($emailChain as $emailChainItem) {

						//get content of current email template							

	                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

						//$emailTemplateContent = $emailTemplate['content'];  

						$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];

						$code = MW_FollowUpEmail_Helper_Data::encryptCode($customer['customer_email'],'order',$order->getId());

						$linkDirect = $store->getUrl('followupemail/index/direct', array('code' => $code));

						$params = array();

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

						//$content = $this->_prepareContentEmail($emailTemplate,$productIds,$order,$customer,$linkDirect);

						$content = "";

						// $scheduledAt, $sentAt, $ruleId, $orderId, $senderName, $recipientName, $recipientEmail, $subject, $content, $params
						if($customer['customer_email'] == "") continue;
						if($this->_checkExistQueueEmail($rule['rule_id'],$order->getId(),$customer['customer_email'],$emailTemplate['code'],0)){
								$coupon = Mage::helper('followupemail/coupon')->generateCode($rule,$customer['customer_email']);
								$params['coupon'] = $coupon;
								$queue->add(

			                        time() + $timeSent * 60,

									$rule['rule_id'],

									$order->getId(),

									$emailTemplate['sender_name'],

									$emailTemplate['sender_email'],

									$customer['customer_name'],				

									$customer['customer_email'],

									$emailTemplate['subject'],				

									$content,

									serialize($params),

									$emailTemplate['code'],

									0,

									$code,
									"",
									$coupon

		                    	);
						}	                   			               

					}
					
				}				
				// end if event Status
			}		

		}		

	}

	

	public function eventDeleteQueueSpecial($eventStatus,$email,$sku,$groupId = 0,$storeId = 0,$abandoncart = 0){
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByCanecelEvent($eventStatus,$storeId,$groupId)->getData();		

		if(is_array($rules)){

			foreach($rules as $rule){								

				$cancelEvent = explode(',',$rule["cancel_event"]);			

				if(in_array($eventStatus,$cancelEvent)){

					$queue = Mage::getModel('followupemail/emailqueue');

														

						$queueEmails = $queue->getCollection()

							->addFieldToFilter('rule_id', $rule['rule_id'])											

							->addFieldToFilter('recipient_email', $email)

                            ->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY)
							->addFieldToFilter('is_abandoncart', $abandoncart);

						$queueEmails->load();

						

						foreach($queueEmails->getData() as $queueEmail){							

							 $deleteQueue = Mage::getModel('followupemail/emailqueue')->load($queueEmail['queue_id']);

							 $deleteQueue->delete();

						}							

				}			

			}		

		}				

	}

	
	private function _checkAbandonedCarts()

    {       		
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		
		if(!$config) return false;
		
		$this->_intTime();

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

            $this->_now - (24 * 3600)))

            /*->where('q.updated_at < ?', date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,

            $now - $intTimeLastHour))*/			

            ->where('q.items_count>0')

            ->where('q.customer_email IS NOT NULL OR a.email IS NOT NULL')

            ->where('i.parent_item_id IS NULL')       

            ->group('q.entity_id')

            ->order('updated_at');		

		//mage::log(date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,$now - ($intFromTimeHour+$intTimeLastHour)));		
        $carts = $read->fetchAll($select);
//mage::log($carts);
		$storeId = Mage::app()->getStore()->getStoreId();		

		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		
		
		$customerEmails = Mage::getModel('customer/customer')->getCollection()->getColumnValues("email");		     
		foreach ($carts as $cart) {				
			$rules = array();
			
			$timeAbandonCart = strtotime($cart['updated_at'])+$this->_intTimeLast;
			
			//if($timeAbandonCart > time()) continue;

			$store = Mage::getModel('core/store')->load($storeId);

            $productIds = explode(',', $cart['product_ids']);   

			$customerInfo = $this->_getCustomer($cart['customer_id'],null);      

			if($cart['customer_email'] == "") continue;
			
			if(in_array($cart['customer_email'],$customerEmails)){
				$rules = Mage::getModel('followupemail/rules')->getCollection()->loadRulesByEvent(MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::EVENT_TYPE_ABANDON_CART,$cart['store_id'],$groupId)->getData();
				
			}			
			else{
				$rules = Mage::getModel('followupemail/rules')->getCollection()->loadRulesByEvent(MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::EVENT_TYPE_ABANDON_CART_FOR_GUEST,$cart['store_id'],$groupId)->getData();	
			
			}						 
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
                    $recipentName = $cart['customer_firstname'].' '.$cart['customer_lastname'];                    
                    $pos = strpos(strtolower($recipentName), "n/a");
                    if ($pos !== false) {
                      $recipentName = "";  
                    }				
					if($this->_checkExistQueueEmail($rule['rule_id'],0,$cart['customer_email'],$emailTemplate['code'],1)){	
                           
							$coupon = Mage::helper('followupemail/coupon')->generateCode($rule,$cart['customer_email']);
							$params['coupon'] = $coupon;
							$queue->add(

		                        time() + $timeSent * 60,

								$rule['rule_id'],

								0,

								$emailTemplate['sender_name'],

								$emailTemplate['sender_email'],

								$recipentName,				

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

					}                   			               

				}

			}			

		}		

    }

	

	public function eventDeleteQueue($arvgs){

		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;

		$order = $arvgs->getOrder();

		$eventStatus = "order_status_".$order->getStatus();		

		$storeId = Mage::app()->getStore()->getStoreId();

		$groupId = $order->getCustomerGroupId();

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByCanecelEvent($eventStatus,$storeId,$groupId)->getData();

		if(is_array($rules)){

			foreach($rules as $rule){								

				$cancelEvent = explode(',',$rule["cancel_event"]);			

				if(in_array($eventStatus,$cancelEvent)){					

		            $queue = Mage::getModel('followupemail/emailqueue');		           				

						$queueEmails = $queue->getCollection()

							->addFieldToFilter('rule_id', $rule['rule_id'])

							->addFieldToFilter('order_id', $order->getId())

							->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY);							

						$queueEmails->load();

						

						foreach($queueEmails->getData() as $queueEmail){							

							 $deleteQueue = Mage::getModel('followupemail/emailqueue')->load($queueEmail['queue_id']);							 

							 $deleteQueue->delete();

						}								                   			              					

				}			

			}		

		}		

		else{

			if($eventStatus == $rules['event']){

				$queueEmailCollection = Mage::getModel('followupemail/rules')->getCollection();

			}

		}	

	}

	

	public function _checkExistQueueEmail($ruleId,$orderId,$email,$templateEmailId,$isabandoncart){        
		
		//return true;
		$this->_intTime();

		$queueEmailCollection = Mage::getModel('followupemail/emailqueue')->getCollection();		
        $queueEmails = $queueEmailCollection->getQueueEmail($ruleId,$orderId,$email,$templateEmailId,$isabandoncart,$this->_dontsendemailtime)->getData();					
		if(count($queueEmails) > 0){

			return false;		

		}

		return true;

	}

		

	protected function _checkMailSentExist($ruleId,$orderId,$email,$templateEmailId,$isabandoncart){

		$queueEmailCollection = Mage::getModel('followupemail/emailqueue')->getCollection();		

        $queueEmails = $queueEmailCollection->getMailSentExist($ruleId,$orderId,$email,$templateEmailId,$isabandoncart)->getData();			

		if(count($queueEmails) > 0){

			foreach($queueEmails as $queueEmail){

				$timeSent = strtotime($queueEmail['sent_at'])+$this->_dontsendemailhours;				

				if($timeSent > $this->_now) return false;

			}			

		}

		return true;		

	}	

	

	public function mailFreeDownload($arvgs){		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		
		$this->_intTime();

		$data = $arvgs->getData();

		$this->deleteQueueFreeTrial($data,MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_DOWNLOAD);

		Mage::helper('customerlogs')->addActivity('Free Download', 'Free Download', $data['sku'], $data['title_download']);	

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();

		$storeId = Mage::app()->getStore()->getStoreId();		

		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();		

        $rules = $rulecollection->loadRulesByEvent(MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_DOWNLOAD,$storeId,$groupId)->getData();

		if(is_array($rules)){

			foreach($rules as $rule){

				$senderInfo = array();

				$senderInfo['sender_name'] = $rule['sender_name'];

				$senderInfo['sender_email'] = $rule['sender_email'];

				$emailChain = unserialize($rule['email_chain']);

				$queue = Mage::getModel('followupemail/emailqueue');

								

				foreach ($emailChain as $emailChainItem) {					

					//get content of current email template							

                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

					//Mage::log($emailTemplate);

					//$emailTemplateContent = $emailTemplate['content'];  

					$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];					

					$code = MW_FollowUpEmail_Helper_Data::encryptCode($data['customer_email'],'',0);

					$params = array();

					$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

					$params['senderInfo'] = $senderInfo;										

					$params['data'] = $data;						

					$params['orderId'] = "";						

					$params['productIds'] = "";																

					$params['customer'] = "";

					$params['customerId'] = "";

					$params['cart'] = "";							

					$params['storeId'] = $storeId;

					$params['code'] = $code;		

					//$content = $this->_prepareContentEmailFreeTrialDownload($emailTemplate,$data);

					$content = "";

					// $scheduledAt, $sentAt, $ruleId, $orderId, $senderName, $recipientName, $recipientEmail, $subject, $content, $params
					if($data['customer_email'] == "") continue;
					if($this->_checkExistQueueEmail($rule['rule_id'],0,$data['customer_email'],$emailTemplate['code'],0)){

							$coupon = Mage::helper('followupemail/coupon')->generateCode($rule,$data['customer_email']);
							$params['coupon'] = $coupon;
							$queue->add(

		                        time() + $timeSent * 60,

								$rule['rule_id'],

								0,

								$emailTemplate['sender_name'],

								$emailTemplate['sender_email'],

								$data['customer_name'],				

								$data['customer_email'],

								$emailTemplate['subject'],				

								$content,

								serialize($params),

								$emailTemplate['code'],

								0,

								$code,

								$data['sku'],
								$coupon

	                    	);	

						//}

					}                 			               

				}

			}

		}

	}

	

	public function mailFreeTrialRequest($arvgs){		

		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$this->_intTime();

		$data = $arvgs->getData();

		$this->deleteQueueFreeTrial($data,MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_TRIAL_REQUEST);

		Mage::helper('customerlogs')->addActivity('Free Trial', 'Free Installation', $data['sku'], $data['title_download']);	

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();

		$storeId = Mage::app()->getStore()->getStoreId();		

		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();		

        $rules = $rulecollection->loadRulesByEvent(MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_TRIAL_REQUEST,$storeId,$groupId)->getData();

		if(is_array($rules)){

			foreach($rules as $rule){

				$senderInfo = array();

				$senderInfo['sender_name'] = $rule['sender_name'];

				$senderInfo['sender_email'] = $rule['sender_email'];

				$emailChain = unserialize($rule['email_chain']);

				$queue = Mage::getModel('followupemail/emailqueue');

								

				foreach ($emailChain as $emailChainItem) {					

					//get content of current email template							

                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

					//Mage::log($emailTemplate);

					//$emailTemplateContent = $emailTemplate['content'];  

					$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];					

					$code = MW_FollowUpEmail_Helper_Data::encryptCode($data['customer_email'],'',0);

					$params = array();

					$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

					$params['senderInfo'] = $senderInfo;										

					$params['data'] = $data;						

					$params['orderId'] = "";						

					$params['productIds'] = "";						

					$params['storeId'] = $storeId;

					$params['code'] = $code;					

					$params['customer'] = "";

					$params['customerId'] = "";

					$params['cart'] = "";	

					//$content = $this->_prepareContentEmailFreeTrialDownload($emailTemplate,$data);

					$content = "";

					// $scheduledAt, $sentAt, $ruleId, $orderId, $senderName, $recipientName, $recipientEmail, $subject, $content, $params
					if($data['customer_email'] == "") continue;
					if($this->_checkExistQueueEmail($rule['rule_id'],0,$data['customer_email'],$emailTemplate['code'],0)){

						$coupon = Mage::helper('followupemail/coupon')->generateCode($rule,$data['customer_email']);
							$params['coupon'] = $coupon;
							$queue->add(

		                        time() + $timeSent * 60,

								$rule['rule_id'],

								0,

								$emailTemplate['sender_name'],

								$emailTemplate['sender_email'],

								$data['customer_name'],				

								$data['customer_email'],

								$emailTemplate['subject'],				

								$content,

								serialize($params),

								$emailTemplate['code'],

								0,

								$code,

								$data['sku'],
								$coupon

	                    	);	

						//}

					}                 			               

				}

			}

		}

	}

	

	public function mailFreeTrialRemind($arvgs){

		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$this->_intTime();		

		$data = $arvgs->getData();		

		$this->deleteQueueFreeTrial($data,MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_TRIAL_REMIND);

		Mage::helper('customerlogs')->addActivity('Free Trial', 'Remind', $data['sku'], $data['title_download']);	

		$storeId = Mage::app()->getStore()->getStoreId();		

		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByEvent(MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_TRIAL_REMIND,$storeId,$groupId)->getData();

		if(is_array($rules)){

			foreach($rules as $rule){

				$senderInfo = array();

				$senderInfo['sender_name'] = $rule['sender_name'];

				$senderInfo['sender_email'] = $rule['sender_email'];

				$emailChain = unserialize($rule['email_chain']);

				$queue = Mage::getModel('followupemail/emailqueue');

								

				foreach ($emailChain as $emailChainItem) {					

					//get content of current email template							

                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

					//Mage::log($emailTemplate);

					//$emailTemplateContent = $emailTemplate['content'];  

					$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];					

					$code = MW_FollowUpEmail_Helper_Data::encryptCode($data['customer_email'],'',0);

					$params = array();

					$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

					$params['senderInfo'] = $senderInfo;										

					$params['data'] = $data;						

					$params['orderId'] = "";						

					$params['productIds'] = "";						

					$params['storeId'] = $storeId;

					$params['code'] = $code;					

					$params['customer'] = "";

					$params['customerId'] = "";

					$params['cart'] = "";	

					//$content = $this->_prepareContentEmailFreeTrialDownload($emailTemplate,$data);

					$content = "";										

					// $scheduledAt, $sentAt, $ruleId, $orderId, $senderName, $recipientName, $recipientEmail, $subject, $content, $params
					if($data['customer_email'] == "") continue;
					if($this->_checkExistQueueEmail($rule['rule_id'],0,$data['customer_email'],$emailTemplate['code'],0)){

						$coupon = Mage::helper('followupemail/coupon')->generateCode($rule,$data['customer_email']);
							$params['coupon'] = $coupon;
							$queue->add(

		                        time() + $timeSent * 60,

								$rule['rule_id'],

								0,

								$emailTemplate['sender_name'],

								$emailTemplate['sender_email'],

								$data['customer_name'],				

								$data['customer_email'],

								$emailTemplate['subject'],				

								$content,

								serialize($params),

								$emailTemplate['code'],

								0,

								$code,

								$data['sku'],
								$coupon

	                    	);

						//}	

					}                 			               

				}

			}

		}

	} 

	

	public function mailFreeTrialDownload($arvgs){	
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$this->_intTime();		

		$data = $arvgs->getData();

		$this->deleteQueueFreeTrial($data,MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_TRIAL_DOWNLOAD);

		Mage::helper('customerlogs')->addActivity('Free Trial', 'Download', $data['sku'], $data['title_download']);	

		$storeId = Mage::app()->getStore()->getStoreId();

		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByEvent(MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::FREE_TRIAL_DOWNLOAD,$storeId,$groupId)->getData();

		if(is_array($rules)){

			foreach($rules as $rule){

				$senderInfo = array();

				$senderInfo['sender_name'] = $rule['sender_name'];

				$senderInfo['sender_email'] = $rule['sender_email'];

				$emailChain = unserialize($rule['email_chain']);

				$queue = Mage::getModel('followupemail/emailqueue');

								

				foreach ($emailChain as $emailChainItem) {					

					//get content of current email template							

                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

					//Mage::log($emailTemplate);

					//$emailTemplateContent = $emailTemplate['content'];  

					$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];					

					$code = MW_FollowUpEmail_Helper_Data::encryptCode($data['customer_email'],'',0);

					$params = array();

					$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

					$params['senderInfo'] = $senderInfo;										

					$params['data'] = $data;						

					$params['orderId'] = "";						

					$params['productIds'] = "";						

					$params['storeId'] = $storeId;

					$params['code'] = $code;						

					$params['customer'] = "";

					$params['customerId'] = "";

					$params['cart'] = "";	

					//$content = $this->_prepareContentEmailFreeTrialDownload($emailTemplate,$data);

					$content = "";

					if($data['customer_email'] == "") continue;

					if($this->_checkExistQueueEmail($rule['rule_id'],0,$data['customer_email'],$emailTemplate['code'],0)){

						$coupon = Mage::helper('followupemail/coupon')->generateCode($rule,$data['customer_email']);
							$params['coupon'] = $coupon;
							$queue->add(

		                        time() + $timeSent * 60,

								$rule['rule_id'],

								0,

								$emailTemplate['sender_name'],

								$emailTemplate['sender_email'],

								$data['customer_name'],				

								$data['customer_email'],

								$emailTemplate['subject'],				

								$content,

								serialize($params),

								$emailTemplate['code'],

								0,

								$code,

								$data['sku'],
								$coupon

	                    	);	

						//}

					}                 			               

				}

			}

		}				

	}

	

	protected function deleteQueueFreeTrial($data,$event){		
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$storeId = Mage::app()->getStore()->getStoreId();

		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByCanecelEvent($event,$storeId,$groupId)->getData();					

		if(is_array($rules)){

			foreach($rules as $rule){														

	            $queue = Mage::getModel('followupemail/emailqueue');		           				

				$queueEmails = $queue->getCollection()

					->addFieldToFilter('rule_id', $rule['rule_id'])

					->addFieldToFilter('recipient_email', $data["customer_email"])

					->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY);

				$queueEmails->load();						

				foreach($queueEmails->getData() as $queueEmail){							

					$deleteQueue = Mage::getModel('followupemail/emailqueue')->load($queueEmail['queue_id']);

					$deleteQueue->delete();					

				}								                   			              									

			}		

		}

	}	

	

	public function _getCustomer($customerId,$order){

		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$customerInfo = array();		

        if ($customerId) {

            $customer = Mage::getModel('customer/customer')->load($customerId);

			if ($customer) {    				 

	            $middlename = $customer->getMiddlename();

	            $customerInfo['customer_name'] = $customer->getFirstname() . ' ' . ($middlename ? $middlename . ' ' : '') . $customer->getLastname();

				$customerInfo['first_name'] = $customer->getFirstname();

				$customerInfo['last_name'] = $customer->getLastname();

	            $customerInfo['customer_email'] = $customer->getEmail();

				$customerAddressId = $customer->getDefaultBilling();

				$address = array();

				$htmlAddress = "";

				if ($customerAddressId){

				       $address = Mage::getModel('customer/address')->load($customerAddressId);

				       $htmlAddress = $address->format('html');

				} 

				$customerInfo['default_address'] = $htmlAddress;

				$customerInfo['city'] = $address['city'];

				$customerInfo['state'] = $address['region'];

				$customerInfo['zip_code'] = $address['postcode'];

				$countryName = Mage::getModel('directory/country')->load($address['country_id'])->getName(); 

				$customerInfo['country'] = $countryName;

	        }

        }
		else{
			if($order != null){
				$orderAddress = MW_FollowUpEmail_Helper_Data::getOrderAddress($order, 'billing');
	            if (!$orderAddress)
	                $orderAddress = MW_FollowUpEmail_Helper_Data::getOrderAddress($order, 'shipping');
	     
				$middlename = $orderAddress->getMiddlename();
				$customerInfo['customer_name'] = $orderAddress->getFirstname() . ' ' . ($middlename ? $middlename . ' ' : '') . $orderAddress->getLastname();				
				$customerInfo['customer_email'] = $order->getCustomerEmail();
				$customerInfo['first_name'] = $orderAddress->getFirstname();
				$customerInfo['last_name'] = $orderAddress->getLastname();
				$customerInfo['default_address'] = "";
				$customerInfo['city'] = $orderAddress->getCity();
				$customerInfo['state'] = $orderAddress->getRegionId();
				$customerInfo['zip_code'] = $orderAddress->getPostcode();
				$countryName = Mage::getModel('directory/country')->load($orderAddress->getCountryId())->getName(); 
				$customerInfo['country'] = $countryName;	
			}			
		}

        return $customerInfo;

	}		   	

	

	public function cartUpdated($arvgs){
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;                              
		$eventStatus = MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::EVENT_TYPE_CART_UPDATED;
        
        $storeId = Mage::app()->getStore()->getStoreId();
        $email =  $arvgs->getCart()->getQuote()->getCustomerEmail();           
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {

			$customer = Mage::getSingleton('customer/session')->getCustomer();

			$email = $customer->getEmail();					

			$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();			

		}
        else{
            //$email = $arvgs->getCart()->getQuote()->getCustomerEmail();
            $groupId = 0;
            
        }
        
        
        $rulecollection = Mage::getModel('followupemail/rules')->getCollection();        

        $rules = $rulecollection->loadRulesByCanecelEvent($eventStatus,$storeId,$groupId)->getData();            

        if(is_array($rules)){

            foreach($rules as $rule){                                                        

                $queue = Mage::getModel('followupemail/emailqueue');                                   

                $queueEmails = $queue->getCollection()

                    ->addFieldToFilter('rule_id', $rule['rule_id'])

                    ->addFieldToFilter('recipient_email', $email)

                    ->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY);

                $queueEmails->load();                        

                foreach($queueEmails->getData() as $queueEmail){                            

                     $deleteQueue = Mage::getModel('followupemail/emailqueue')->load($queueEmail['queue_id']);

                     $deleteQueue->delete();

                }                                                                                                                 

            }        

        }

	}

	

	public function customerLogin($arvgs){
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$customer = $arvgs->getCustomer();

		$data = $customer->getData();

		$session = Mage::getSingleton('core/session');	

		if($session->getCheckFollowUpEmail() == ""){

			$session->setCheckFollowUpEmail($data['email']);

			$this->eventLoginDelete($data);						

		}			

	}

	

	public function customerLogout($arvgs){
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$session = Mage::getSingleton('core/session');	

		$session->setCheckFollowUpEmail("");

	}

	public function customerUpdated($arvgs){		
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$this->_intTime();
				
		if($arvgs->getCustomer() != ""){
			$customerSession = $arvgs->getCustomer()->getData();	
		}		
		else{
			$customerSession = Mage::helper('customer')->getCustomer()->getData();	
		}

		$eventStatus = MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::CUSTOMER_ACCOUNT_UPDATED;		

		$storeId = $customerSession['store_id'];

		$groupId = $customerSession['group_id'];

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByEvent($eventStatus,$storeId,$groupId)->getData();	
		
		$customerInfo = $this->_getCustomer($customerSession['entity_id'],null);		

		if(is_array($rules)){

			foreach($rules as $rule){	
			
				if(!Mage::getModel('followupemail/validate')->validate(unserialize($rule["conditions_serialized"]),null,null,$customerSession['entity_id'])) continue;

				$senderInfo = array();

				$senderInfo['sender_name'] = $rule['sender_name'];

				$senderInfo['sender_email'] = $rule['sender_email'];													

	            $emailChain = unserialize($rule['email_chain']);

				$queue = Mage::getModel('followupemail/emailqueue');								

				foreach ($emailChain as $emailChainItem) {					

					//get content of current email template							

                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);

					//Mage::log($emailTemplate);

					//$emailTemplateContent = $emailTemplate['content'];  

					$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];	

					$code = MW_FollowUpEmail_Helper_Data::encryptCode($customerInfo['customer_email'],'',0);

					$params = array();

					$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

					$params['senderInfo'] = $senderInfo;

					$params['productIds'] = "";

					$params['orderId'] = "";

					$params['data'] = "";

					$params['storeId'] = $storeId;

					$params['code'] = $code;

					$params['customer'] = $customerInfo;

					$params['customerId'] = $customerSession['entity_id'];

					$params['cart'] = "";

					//$content = $this->_prepareContentEmail($emailTemplate,array(),0,$customerInfo,"");					

					$content = "";					

					if($customerSession['email'] == "") continue;
					if($this->_checkExistQueueEmail($rule['rule_id'],0,$customerSession['email'],$emailTemplate['code'],0)){
						$coupon = Mage::helper('followupemail/coupon')->generateCode($rule,$customerSession['email']);
						$params['coupon'] = $coupon;
					$queue->add(

                        time() + $timeSent * 60,

						$rule['rule_id'],

						0,

						$emailTemplate['sender_name'],

						$emailTemplate['sender_email'],

						$customerSession['firstname'].' '.$customerSession['lastname'],				

						$customerSession['email'],

						$emailTemplate['subject'],				

						$content,

						serialize($params),

						$emailTemplate['code'],

						1,

						$code,
						"",
						$coupon

                	);	
					
					}					                 			              

				}								                   			              									

			}		

		}	
	}

	public function newCustomerSignedUp($arvgs){			
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$this->_intTime();
		if($arvgs->getCustomer() != ""){
			$customer = $arvgs->getCustomer()->getData();
		}
		else{
			$customer = Mage::helper('customer')->getCustomer()->getData();	
		}
		
		$eventStatus = MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::NEW_CUSTOMER_SIGNED_UP;		

		$storeId = $customer['store_id'];

		$groupId = $customer['group_id'];

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByEvent($eventStatus,$storeId,$groupId)->getData();	

		$customerInfo = $this->_getCustomer($customer['entity_id'],null);		

		if(is_array($rules)){

			foreach($rules as $rule){	
				
				if(!Mage::getModel('followupemail/validate')->validate(unserialize($rule["conditions_serialized"]),null,null,$customer['entity_id'])) continue;
				$senderInfo = array();

				$senderInfo['sender_name'] = $rule['sender_name'];

				$senderInfo['sender_email'] = $rule['sender_email'];													

	            $emailChain = unserialize($rule['email_chain']);

				$queue = Mage::getModel('followupemail/emailqueue');								

				foreach ($emailChain as $emailChainItem) {					

					//get content of current email template							

                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);
					
					//Mage::log($emailTemplate);

					//$emailTemplateContent = $emailTemplate['content'];  

					$timeSent = $emailChainItem['DAYS'] * 1440 + $emailChainItem['HOURS'] * 60 + $emailChainItem['MINUTES'];	

					$code = MW_FollowUpEmail_Helper_Data::encryptCode($customerInfo['customer_email'],'',0);

					$params = array();

					$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

					$params['senderInfo'] = $senderInfo;

					$params['productIds'] = "";

					$params['orderId'] = "";

					$params['data'] = "";

					$params['storeId'] = $storeId;

					$params['code'] = $code;

					$params['customer'] = $customerInfo;

					$params['customerId'] = $customer['entity_id'];

					$params['cart'] = "";

					//$content = $this->_prepareContentEmail($emailTemplate,array(),0,$customerInfo,"");					

					$content = "";					

					if($customer['email'] == "") continue;
					if($this->_checkExistQueueEmail($rule['rule_id'],0,$customer['email'],$emailTemplate['code'],0)){
					$coupon = Mage::helper('followupemail/coupon')->generateCode($rule,$customer['email']);						
					$params['coupon'] = $coupon;
					$queue->add(

                        time() + $timeSent * 60,

						$rule['rule_id'],

						0,

						$emailTemplate['sender_name'],

						$emailTemplate['sender_email'],

						$customer['firstname'].' '.$customer['lastname'],				

						$customer['email'],

						$emailTemplate['subject'],				

						$content,

						serialize($params),

						$emailTemplate['code'],

						1,

						$code,
						"",
						$coupon

                	);
					}						                 			              

				}								                   			              									

			}		

		}		

	}

	

	public function eventLoginDelete($data){		
		
		$config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		
		$eventStatus = MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::CUSTOMER_LOGGED_IN;		

		$storeId = Mage::app()->getStore()->getStoreId();

		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();		

        $rules = $rulecollection->loadRulesByCanecelEvent($eventStatus,$storeId,$groupId)->getData();			

		if(is_array($rules)){

			foreach($rules as $rule){														

	            $queue = Mage::getModel('followupemail/emailqueue');		           				

				$queueEmails = $queue->getCollection()

					->addFieldToFilter('rule_id', $rule['rule_id'])

					->addFieldToFilter('recipient_email', $data["email"])

					->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY);

				$queueEmails->load();						

				foreach($queueEmails->getData() as $queueEmail){							

					$deleteQueue = Mage::getModel('followupemail/emailqueue')->load($queueEmail['queue_id']);

					$deleteQueue->delete();

					$quote = Mage::getSingleton('checkout/session')->loadCustomerQuote();

					$quoteId = $quote->getQuote()->getId();

					//mage::log($quote);

					$conn     = Mage::getModel('core/resource')->getConnection('core_write');

					$resource = Mage::getSingleton('core/resource');

					$tblQuote = $resource->getTableName('sales/quote');

					$dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

					$currentDate = date($dateFormatIso, time());

					$sql      = "UPDATE `$tblQuote` SET `updated_at`='$currentDate' WHERE `entity_id`='$quoteId'";

					$conn->query($sql);

				}								                   			              									

			}		

		}		

	}
	
	
	protected function _checkBirthdays()
    {
        $config = Mage::getStoreConfig('followupemail/config/enabled');
		if(!$config) return false;
		$rulecollection = Mage::getModel('followupemail/rules')->getCollection();	
		$eventStatus = MW_FollowUpEmail_Model_System_Config_Eventfollowupemail::CUSTOMER_BIRTHDAY;	
		$storeId = Mage::app()->getStore()->getStoreId();		
		$rules = $rulecollection->loadRulesByEvent($eventStatus,$storeId,"")->getData();	
        $customerEntityTypeID = Mage::getModel('eav/entity_type')->loadByCode('customer')->getId();
        $customerDateOfBirthAttributeId = Mage::getModel('eav/entity_attribute')->loadByCode($customerEntityTypeID, 'dob')->getId();

        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');

        $customer_entity = $resource->getTableName('customer/entity');
        $dobTableName = $customer_entity.'_datetime';

        $time = time();

        foreach($rules as $rule)
        {           
			$senderInfo = array();

			$senderInfo['sender_email'] = $rule['sender_email'];

			$senderInfo['sender_name'] = $rule['sender_name'];
            foreach(unserialize($rule['email_chain']) as $emailChainItem)
            {
                $select = $read->select()
                    ->from(array('dob' => $dobTableName), array('entity_id', 'value'))
                    ->join(array('customer' => $customer_entity),
                        'customer.entity_id=dob.entity_id AND customer.entity_type_id=dob.entity_type_id',
						array(
			            'store_id' => 'customer.store_id',

			            'email' => 'customer.email',

			            'group_id' => 'customer.group_id'))

                    ->where('dob.entity_type_id=?', $customerEntityTypeID)
                    ->where('dob.attribute_id=?', $customerDateOfBirthAttributeId)
                    ->where('DATE_FORMAT(dob.value, "%m-%d")=?', date('m-d', $time - (int)($emailChainItem['BEFORE']*$emailChainItem['DAYS']*86400)));
                $birthDays = $read->fetchAll($select);
				$queue = Mage::getModel('followupemail/emailqueue');		
                if(count($birthDays))
                {
                    $params = array();
                    foreach($birthDays as $birthDay)
                    {						
						 if(!Mage::getModel('followupemail/validate')->validate(unserialize($rule["conditions_serialized"]),null,null,$birthDay['entity_id'])) continue;
                        //get content of current email template							

	                	$emailTemplate = Mage::getModel('followupemail/rules')->getTemplate($emailChainItem['TEMPLATE_ID'],$rule);
						$customerInfo = $this->_getCustomer($birthDay['entity_id'],null);
				
						//$timeSent = (int)($emailChainItem['BEFORE']*$emailChainItem['DAYS']*1400);	

						$code = MW_FollowUpEmail_Helper_Data::encryptCode($birthDay['email'],'',0);

						$params = array();

						$params['templateEmailId'] = $emailChainItem['TEMPLATE_ID'];

						$params['senderInfo'] = $senderInfo;

						$params['productIds'] = "";

						$params['orderId'] = "";

						$params['data'] = "";

						$params['storeId'] = $birthDay['store_id'];

						$params['code'] = $code;

						$params['customer'] = $customerInfo;

						$params['customerId'] = $birthDay['entity_id'];

						$params['cart'] = "";									

						$content = "";					

						if($birthDay['email'] == "") continue;                    

                        if($this->_checkExistQueueEmail($rule['rule_id'],0,$birthDay['email'],$emailTemplate['code'],time(),1)){

							$coupon = Mage::helper('followupemail/coupon')->generateCode($rule,$birthDay['email']);
							$params['coupon'] = $coupon;
							$queue->add(

		                        time(),

								$rule['rule_id'],

								0,

								$emailTemplate['sender_name'],

								$emailTemplate['sender_email'],

								$customerInfo['first_name'].' '.$customerInfo['last_name'],				

								$customerInfo['customer_email'],

								$emailTemplate['subject'],				

								$content,

								serialize($params),

								$emailTemplate['code'],

								1,

								$code,
								"",
								$coupon

	                    	);
						}  
                    }
                }               
            }
        }
    }
	
	
	//CheckStatus
	public function checkStatus($arvgs){
		$action = $arvgs->getControllerAction();
		$qid = $action->getRequest()->getParam('qid');
		if($qid != ""){
			$queueId = Mage::getSingleton('core/session')->setQueueId($qid);
			$model = Mage::getModel('followupemail/emailqueue');			
			$model->load($qid);
			if($model->getCustomerResponse() != MW_FollowUpEmail_Model_System_Config_Response::QUEUE_STATUS_CLICKED && $model->getCustomerResponse() != MW_FollowUpEmail_Model_System_Config_Response::QUEUE_STATUS_PURCHASED){
				$model->setCustomerResponse(MW_FollowUpEmail_Model_System_Config_Response::QUEUE_STATUS_CLICKED);
				$model->save();
			}			
		}
	}
	

	public function runCron()
    {
    mage::log("runCron")		;
		$this->_intTime();

		$this->_checkAbandonedCarts();	
		$this->_checkBirthdays();	

        $currenttime = date('Y-m-d H:i:s',time());    	
		
		$coupons = Mage::getModel('followupemail/coupons')->getCollection()

											->addFieldToFilter('coupon_status',MW_FollowUpEmail_Model_System_Config_Statuscoupon::COUPON_STATUS_SENT)
											
											->addFieldToFilter('expiration_date',array('to' => $currenttime));
											
		foreach($coupons->load()->getData() as $c){
			$coupon = Mage::getModel('followupemail/coupons');
			$coupon->load($c['coupon_id'])
        	->setCouponStatus(MW_FollowUpEmail_Model_System_Config_Statuscoupon::COUPON_STATUS_EXPIRED)
        	->save();
		}
		
    	$queueEmails = Mage::getModel('followupemail/emailqueue')->getCollection()
											->addFieldToFilter('status',MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY)
											->addFieldToFilter('scheduled_at',array('to' => $currenttime));

		foreach($queueEmails->load()->getData() as $d){
      	//Mage::log("FUE:runCron:".$d['queue_id']);
			$emailBefore = Mage::getModel('followupemail/emailqueue')->load($d['queue_id']);
      		$params = $emailBefore->getParams();
			if (@unserialize($params) === FALSE){
        		//Mage::log("FUE:runCron:".$d['queue_id'].':updateparams');
				Mage::getModel('followupemail/observer')->updateParamsEmail($emailBefore);
			}

			$result = Mage::getModel('followupemail/emailqueue')->load($d['queue_id'])->send();	
			if($result === true){}
			else if($result == 3){
				Mage::log("FUE log:");
				Mage::log("Email ".$d['recipient_email']." sent error because:");
				Mage::log("This email is not sent to customer neither BBCed to anyone.");
			}
			else{
				Mage::log("FUE log:");
				Mage::log("Email ".$d['recipient_email']." ccould not be sent.");				
			}

		}

    }
	
	public function runCronCleanMail(){
		$this->_intTime();
		$time = date('Y-m-d H:i:s',time() - $this->_intTimeCleanMail);    	

    	$queueEmails = Mage::getModel('followupemail/emailqueue')->getCollection()
											->addFieldToFilter('sent_at',array('to' => $time));
											$queueEmails->getSelect()->where('status = 2 or status = 3');											

		foreach($queueEmails->load()->getData() as $d){

			$deleteQueue = Mage::getModel('followupemail/emailqueue')->load($d['queue_id']);

			$deleteQueue->delete();

		}
	}
	
	

	// Function update param fix bug

	public function updateParamsEmail($email){

		$model = Mage::getModel('followupemail/emailqueue');

		$ruleId = $email->getRuleId();

		$orderId = $email->getOrderId();

		$productIds = array();

		$customerId = array();

		$cart = null;

		if($orderId > 0){

			$order = Mage::getModel('sales/order')->load($orderId);

			$items = $order->getAllItems();

			foreach ($items as $itemId => $item){              
				if ($item->getParentItem()) continue;
               $productIds[]=$item->getProductId();               

            }

			$storeId = $order->getStoreId();

			$customerId = $order->getCustomerId();

		}

		else{

			$orderId = "";

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

	            ))

	            ->joinInner(array('i' => $resource->getTableName('sales/quote_item')), 'q.entity_id=i.quote_id', array(

	            'product_ids' => new Zend_Db_Expr('GROUP_CONCAT(i.product_id)'),

	            'item_ids' => new Zend_Db_Expr('GROUP_CONCAT(i.item_id)')

	        ))

	            ->where('q.is_active=1') 

				->where('q.customer_email = ?',$email->getRecipientEmail())           

	            /*->where('q.updated_at < ?', date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,

	            $now - $intTimeLastHour))*/			

	            ->where('q.items_count>0')	            

	            ->where('i.parent_item_id IS NULL')

	            ->group('q.entity_id')

	            ->order('updated_at');		

			//mage::log(date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,$now - ($intFromTimeHour+$intTimeLastHour)));

	        $carts = $read->fetchAll($select);

			foreach ($carts as $_cart) {				

	            $productIds = explode(',', $_cart['product_ids']);  

				$cart = $_cart;

				$customerId = $_cart['customer_id'];

				$storeId = $_cart['store_id'];

			}

		}		

		$emailTemplate  = Mage::getModel('core/email_template')->loadByCode($email->getEmailtemplateId());		

		$rule = Mage::getModel('followupemail/rules')->load($ruleId);

		$senderInfo = array();

		$senderInfo['sender_name'] = $rule->getSenderName();

		$senderInfo['sender_email'] = $rule->getSenderEmail();

		$params = array();

		$params['templateEmailId'] = 'email:'.$emailTemplate->getTemplateId();

		$params['senderInfo'] = $senderInfo;

		$params['productIds'] = $productIds;

		$params['orderId'] = $orderId;						

		$params['data'] = "";

		$params['customer'] = "";

		$params['customerId'] = $customerId;

		$params['cart'] = $cart;

		$params['storeId'] = $storeId;

		$params['code'] = $email->getCode();

		$model->load($email->getQueueId());

		$model->setParams(serialize($params));

		//$model->setParams("");

		$model->save();

	}

}