<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_Model_Log_Details extends Mage_Core_Model_Abstract
{
    public function _construct()
    {    
        $this->_init('amaudit/log_details', 'entity_id');
    }
    
    public function isInCollection($idLog, $name, $model)
    {
        $collection = $this->getCollection();
        $collection->addFieldToFilter('log_id', array('in' => $idLog)); 
        $collection->addFieldToFilter('name', array('in' => $name)); 
        $collection->addFieldToFilter('model', array('in' => $model)); 
        if($collection->count() > 0){
            return true;
        }
        else{
            return false;
        }
    }
}