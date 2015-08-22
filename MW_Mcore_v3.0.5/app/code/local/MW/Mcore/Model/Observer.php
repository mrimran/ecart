<?php
class MW_Mcore_Model_Observer
{	
	public function adloginsuccess($o)
	{				
		Mage::helper('mcore')->updatestatus();
	}	
	
	public function logoutupdate($o){		
		Mage::helper('mcore')->updatestatus();
		Mage::helper('mcore')->resetSpecNotification();	
	}	
	
	public function updateStatus()
	{
		Mage::helper('mcore')->getServerNotification();
		Mage::helper('mcore')->updatestatus();	
	}
	
}