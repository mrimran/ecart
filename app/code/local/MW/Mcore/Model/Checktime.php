<?php
class MW_Mcore_Model_Checktime
{
	public function updateStatus()
	{		
		Mage::helper('mcore')->getServerNotification();
		Mage::helper('mcore')->updatestatus();
	}


	

}