<?php

class MW_Mcore_Model_Notification extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('mcore/notification');
    }
    
    public function _beforeSave()
    {
   		 if (!$this->getId()) {
   			 if($this->getType()=="")   			 
   			 	$this->setType("message");
   			 
	   			 if($this->getTimeApply()==Null)
	   			 {  	   			 	 
	   			 	$this->setTimeApply(now());
	   			 }
   			  }
    }
}