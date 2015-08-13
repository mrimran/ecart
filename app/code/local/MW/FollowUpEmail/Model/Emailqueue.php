<?php

class MW_FollowUpEmail_Model_Emailqueue extends Mage_Core_Model_Abstract
{
    /*
     * Class constructor
     */
	
    public function _construct()
    {
		parent::_construct();
        $this->_init('followupemail/emailqueue');
    }

    const PARAM_SEPARATOR = '##';

    const NAME_VALUE_SEPARATOR = '=';

    

    const VALUE_SEPARATOR = ',';



    public function add($scheduledAt, $ruleId, $orderId, $senderName,$senderEmail, $recipientName, $recipientEmail, $subject, $content, $params,$templateEmailId,$isAbandoncart=0,$code="",$sku="",$coupon_code="")

    {				

        $scheduledAt = date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT, $scheduledAt);

		if($recipientEmail == "") return false;

        $this

            ->setQueueId(null)

			->setStatus(1)            

            ->setCreateDate(date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT, time()))

            ->setScheduledAt($scheduledAt)

            ->setSentAt(null)

			->setRuleId($ruleId)

			->setOrderId($orderId)            

            ->setSenderName($senderName)

            ->setSenderEmail($senderEmail)

            ->setRecipientName($recipientName)

            ->setRecipientEmail($recipientEmail)

            ->setSubject($subject)

            ->setContent($content)                                    

            ->setParams($params)

            ->setEmailtemplate_id($templateEmailId)			

            ->setIs_abandoncart($isAbandoncart)			

            ->setCode($code)		

            ->setSku($sku)	
				
            ->setCouponCode($coupon_code)		

            ->save();



        //Mage::log("email added id={$this->getQueueId()} email=$recipientEmail name=$recipientName ruleId=$ruleId scheduledAt=$scheduledAt GMT", $this);

        return $this->getQueueId();

    }

	

	public function deleteQueueEmail($ruleid,$orderId){

		$queueEmails = $this->getCollection()

			->addFieldToFilter('rule_id', $ruleid)

			->addFieldToFilter('order_id', $orderId);

		$queueEmails->load();

		foreach($queueEmails->getData() as $queueEmail){

		//Mage::log($queueEmail['queue_id']);		 

			 $deleteQueue = Mage::getModel('followupemail/emailqueue');

			 //$deleteQueue = $this->load($queueEmail['queue_id']);

			 //Mage::log(get_class($deleteQueue));

			 //$deleteQueue->delete();

		}

	}

	

	/*

     * Sends email

     * @return bool|Exception Sending result

     */

    public function send()
    {

        if (!$this->getId()) return false;

        $email = Mage::getModel('core/email_template');

        $translate = Mage::getSingleton('core/translate');

        /* @var $translate Mage_Core_Model_Translate */

        $translate->setTranslateInline(false);

		$params = unserialize($this->getParams());

        $email_customer = $this->getRecipientEmail();
        $params['email_customer'] = $email_customer;

        // email content
        $content = Mage::helper('followupemail')->_prepareContentEmail($params,$this->getId());



        $_subject = Mage::helper('followupemail')->_prepareSubjectEmail($params,$this->getSubject());
		
        $subject = htmlspecialchars($_subject);

        $message = nl2br(htmlspecialchars($content));

		$infoEmailRule = Mage::getModel('followupemail/rules')->getAllEmailRulesFromResource($this->getRuleId());

		if($infoEmailRule['send_mail_customer'] == 1){

				$name = array(

	            'name' => $this->getRecipientName(),

	            'email' => $this->getRecipientEmail());
			
		}
		
		else if($infoEmailRule['send_mail_customer'] == 2){			

			if($this->checkNewsletter($this->getRecipientEmail())){

				$name = array(

            	'name' => $this->getRecipientName(),

            	'email' => $this->getRecipientEmail());	

			}

			else{

				$name = array(

            	'name' => '',

            	'email' => '');	

			}		

		}		

		else{

			$name = array(

	            'name' => '',

	            'email' => '');	

		}

        $sender = array(

            'name' => strip_tags($this->getSenderName()),

            'email' => strip_tags($this->getSenderEmail())

        );		        

        $email->setReplyTo($sender['email']);

        $email->setSenderName($sender['name']);

        $email->setSenderEmail($sender['email']);        	

        $_copyTo = Mage::helper('followupemail')->explodeEmailList($infoEmailRule['copy_to_email']);		

        if (!isset($infoEmailRule['send_mail_customer'])) $infoEmailRule['send_mail_customer'] = 1;

        $email->setTemplateSubject($subject);		

        $email->setTemplateText($content);
		
        $email->setDesignConfig(array('area' => 'frontend', 'store' => $params['storeId']));
		
		if($name['email'] == "" && empty($_copyTo)){
			$result = 3; // This email is not sent to customer neither BBCed to anyone
			return $result;
		}
		
        if(empty($_copyTo))

            $recipients = array($name['email']);

        foreach ($_copyTo as $bccEmail) {

            if ($infoEmailRule['send_mail_customer'] == 1) {				

                $recipients = array($name['email']);

                $email->addBcc($bccEmail);

            } else {

                if (empty($recipients)) {

                    $recipients = $bccEmail;

                } else if ($recipients != $bccEmail) {

                    $email->addBcc($bccEmail);

                }

            }

        }


        $result = $email->send(

            $recipients,

            null,

            array(

                'name' => $name['name'],

                'email' => $name['email'],

                'subject' => $subject,

                'message' => $message,
				
				'queueID' => $this->getId()

            )



        );

        $translate->setTranslateInline(true);		

        if ($result) {

            $this->setStatus(2)
			
				->setCustomerResponse(1)

                ->setSentAt(date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT, time()))

				->setContent($content)

                ->save();
				
				$rule = Mage::getModel('followupemail/rules')->load($this->getRuleId());
				
				$day = ((int)$rule->getCouponExpireDays() * 24) * 3600;

        		$expires = date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT, $day + time()); 
				
				$createdate = date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT, time());
				
				$coupon = Mage::getModel('followupemail/coupons');
				
				$couponModel = $coupon->getByCode($this->getCouponCode());
				
				if($couponModel){
				
					$couponId = $couponModel->getCouponId();
				
					$coupon->load($couponId)

				    ->setCouponStatus(MW_FollowUpEmail_Model_System_Config_Statuscoupon::COUPON_STATUS_SENT)
		    
				    ->setExpirationDate($expires) 
						    
				    ->setCreatedAt($createdate) 
		    
				    ->save();
				}
            

        } else {

            $this->setStatus(3)

                ->setSentAt(date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT, time()))

                ->save();            

        }

        return $result;

    }

	

	public function cancel()

    {

        $this->setStatus(4)

            ->setSentAt(date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT, time()))

            ->save();

        return $this;

    }

	

	public function loadByCode($code)

    {

        $id = $this->getResource()->getIdByCode($code);

        if(!$id) return false;

        return $this->load($id);

    }


	protected function checkNewsletter($email){

		$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);		

		return $subscriber->isSubscribed(); 

	}

    //cancel email unsubscribe
    public function ununsubscribe($email){
        $collection_emails = Mage::getModel('followupemail/emailqueue')->getCollection()
                    ->addFieldToFilter('status',MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY)
                    ->addFieldToFilter('recipient_email',$email);

        foreach($collection_emails as $emails){
            $emails->setData('status',MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_CANCELLED);
            $emails->save();
        }
    }

    // Insert Google Analytics all link in email
    public function InsertAnalytics($rule_id,$html){
        $google_param = $this->getGoogleAnalyticsParam($rule_id);
        $campaign_source = $google_param->getCampaignSource();
        $campaign_medium = $google_param->getCampaignMedium();
        $campaign_term = $google_param->getCampaignTerm();
        $campaign_content = $google_param->getCampaignContent();
        $campaign_name = $google_param->getCampaignName();

        preg_match_all("/(?<=href=(\"|'))[^\"']+(?=(\"|'))/", $html, $matches, PREG_SET_ORDER);
        foreach ($matches as $val) {
            $new_url = $val[0].'?utm_source='.$campaign_source.'&utm_medium='.$campaign_medium.'&utm_term='.$campaign_term.'&utm_content='.$campaign_content.'&utm_campaign='.$campaign_name.'';
            $html = str_replace($val[0],$new_url,$html);
        }
        return $html;
    }

    public function getGoogleAnalyticsParam($queueId){
        $collection_email = Mage::getModel('followupemail/emailqueue')->getCollection()
                ->addFieldToFilter('queue_id',$queueId);
        $email = $collection_email->getData();
        $rule_id = $email[0]['rule_id'];
        $collection_rule = Mage::getModel('followupemail/rules')->load($rule_id);
        return $collection_rule;
    }
}

