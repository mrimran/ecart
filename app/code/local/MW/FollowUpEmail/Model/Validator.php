<?php
class MW_FollowUpEmail_Model_Validator extends Mage_SalesRule_Model_Validator{
	public function init($websiteId, $customerGroupId,$couponCode)
    {		
        $this->setWebsiteId($websiteId)
            ->setCustomerGroupId($customerGroupId)
            ->setCouponCode($couponCode);

        $key = $websiteId . '_' . $customerGroupId . '_' . $couponCode;
        if (!isset($this->_rules[$key])) {
			$ruleCollection = Mage::getResourceModel('salesrule/rule_collection')
                ->setValidationFilter($websiteId, $customerGroupId, $couponCode)
                ->load();
			if(count($ruleCollection)>0){				
            	$this->_rules[$key] = $ruleCollection;
			}
			else{					
				$fueCouponRule = Mage::getResourceModel('followupemail/salesrule_collection')
                ->setValidationFilter($websiteId, $customerGroupId, $couponCode)
                ->load();					
				if(count($fueCouponRule) > 0){
					$allowCoupon = false;
					foreach($fueCouponRule as $fueRule){
						$fueCouponCode = $fueRule->getCode();
						$fueCouponData = Mage::getModel('followupemail/coupons')->getByCode($fueCouponCode);
						$email = Mage::getSingleton('customer/session')->getCustomer()->getEmail();						
                        $emailSession = Mage::getSingleton('core/session')->getEmailGuest();
						if($fueCouponData['use_customer'] == $email){
							$allowCoupon = true;
						}
                        if($fueCouponData['use_customer'] == $emailSession){
                            $allowCoupon = true;
                        }
					}
					if($allowCoupon){
						$this->_rules[$key] = $fueCouponRule;
					}
					else{
						$this->_rules[$key] = $ruleCollection;	
					}					
				}
				else{
					$this->_rules[$key] = $ruleCollection;	
				}				
			}
        }
        return $this;
    }
}