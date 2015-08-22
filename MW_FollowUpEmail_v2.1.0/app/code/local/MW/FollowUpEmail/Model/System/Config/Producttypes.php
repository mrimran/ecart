<?php
class MW_FollowUpEmail_Model_System_Config_Producttypes extends Mage_Core_Model_Abstract
{    
    public static function getProductTypes(){
		$values = array();
		$productTypes = Mage::getConfig()->getNode('global/catalog/product/type')->asArray();
		$values['all'] = Mage::helper('followupemail')->__('All types');
    	foreach ($productTypes as $code => $name){			
        	$values[$code] = Mage::helper('followupemail')->__("%s", $name['label']);	
		}			
		
		return $values;
	}
}