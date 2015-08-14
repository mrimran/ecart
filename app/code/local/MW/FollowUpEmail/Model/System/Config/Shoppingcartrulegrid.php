<?php

    class MW_FollowUpEmail_Model_System_Config_Shoppingcartrulegrid extends Mage_Core_Model_Abstract

    {		
        public static function toOptionArray()

        {

			$rulesCollection = Mage::getModel('salesrule/rule')->getResourceCollection()
            ->addFieldToFilter('is_active', 1);
        
	        $result = array();
	        foreach($rulesCollection as $rule)
	            $result[$rule->getRuleId()] = $rule->getName();

	        return $result;

        }
    }

?>