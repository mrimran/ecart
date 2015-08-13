<?php

class MW_FollowUpEmail_Model_Mysql4_Reportemails_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract

{

    public function _construct()

    {

        parent::_construct();

        $this->_init('followupemail/emailqueue');

    }

	public function setDateRange($from, $to)

	{			
			$resource = Mage::getModel('core/resource');

  	  		$coreemail_table = $resource->getTableName('core/email_template');

	        $this->_reset() ->addFieldToFilter('main_table.create_date', array('from' => $from, 'to' => $to, 'datetime' => true));

	        $this->getSelect()->joinLeft(

      							array('coreemail'=>$coreemail_table),'main_table.emailtemplate_id = coreemail.template_code');

	        $this ->addFieldToFilter('status',MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_SENT);
			$this->getSelect()->columns("sum(main_table.status = '2') AS sent");			
			$this->getSelect()->columns("CONCAT(sum(main_table.customer_response = '1'),' (',ROUND((sum(main_table.customer_response = '1')/sum(main_table.status = '2'))*100,2),'%',')') AS unread");
			$this->getSelect()->columns("CONCAT(sum(main_table.customer_response = '2'),' (',ROUND((sum(main_table.customer_response = '2')/sum(main_table.status = '2'))*100,2),'%',')') AS read");
			$this->getSelect()->columns("CONCAT(sum(main_table.customer_response = '3') + sum(main_table.customer_response = '4'),' (',ROUND(((sum(main_table.customer_response = '3') + sum(main_table.customer_response = '4'))/sum(main_table.status = '2'))*100,2),'%',')') AS clicked");
			$this->getSelect()->columns("CONCAT(sum(main_table.customer_response = '4'),' (',ROUND((sum(main_table.customer_response = '4')/sum(main_table.status = '2'))*100,2),'%',')') AS purchased");
			$this->getSelect()->columns("CONCAT(sum(main_table.customer_response = '2') + sum(main_table.customer_response = '3') + sum(main_table.customer_response = '4'),' (',ROUND(((sum(main_table.customer_response = '2') + sum(main_table.customer_response = '3') + sum(main_table.customer_response = '4'))/sum(main_table.status = '2'))*100,2),'%',')')  as readtotal");
	       $this->getSelect()->group(array('main_table.emailtemplate_id'));
			//mage::log($this->getSelect()->__toString());
	        return $this;

	}

 	public function setStoreIds($storeIds)

    {

        return $this;

    }

}