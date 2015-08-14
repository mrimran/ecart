<?php
class MW_FollowUpEmail_Block_Adminhtml_Queue_Grid_Column_Sentatemptydate extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime
{
    public function render(Varien_Object $row)
    {        		
		$queueId = $row->getData("queue_id");
		if($queueId != ""){
			$queue = Mage::getModel('followupemail/emailqueue')->load($queueId);
			if($queue->getStatus() == MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_CANCELLED){
				return Mage::helper('followupemail')->__('Email is cancelled');
			}			
		}
		if($row->getData($this->getColumn()->getIndex()))
            return parent::render($row);
        else return $this->getColumn()->getEmptyText();		    
    }
}		
		