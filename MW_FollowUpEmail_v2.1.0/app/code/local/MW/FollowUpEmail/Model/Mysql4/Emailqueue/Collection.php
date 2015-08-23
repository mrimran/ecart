<?php
class MW_FollowUpEmail_Model_Mysql4_Emailqueue_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    public function _construct() 
    {
        parent::_construct();
        $this->_init('followupemail/emailqueue');
    }

    public function getSelectCountSql() 
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);

        $countSelect->from('', 'COUNT(*)');
        return $countSelect;
    }
	
	public function getQueueEmail($ruleId,$orderId,$email,$templateEmailId,$isabandoncart,$dontsendemailtime){
		//$timeTo = date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,$scheduledAt + $dontsendemailtime);
		//$timeFrom = date(MW_FollowUpEmail_Model_Mysql4_Emailqueue::MYSQL_DATETIME_FORMAT,$scheduledAt - $dontsendemailtime);
		$query = $this->addFieldToFilter('rule_id', $ruleId)
		->addFieldToFilter('order_id', $orderId)		
		->addFieldToFilter('is_abandoncart', $isabandoncart)
		->addFieldToFilter('recipient_email', $email)
		->addFieldToFilter('emailtemplate_id', $templateEmailId);
		//$query->getSelect()->where('scheduled_at < ?', $timeTo);
        //$query->getSelect()->where('scheduled_at > ?', $timeFrom);
		//->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY);
		$query->load();		
		return $query;
	}
	
	/*public function getQueueEmailAbandonCart($ruleId,$orderId,$email,$templateEmailId){		
		$query = $this->addFieldToFilter('recipient_email', $email)
		->addFieldToFilter('is_abandoncart', 1)
		->addFieldToFilter('rule_id', $ruleId)
		->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_READY)
		->addFieldToFilter('emailtemplate_id', $templateEmailId);
		$query->load();				
		return $query;
	}*/
	
	public function getMailSentExist($ruleId,$orderId,$email,$templateEmailId,$isabandoncart){		
		$query = $this->addFieldToFilter('recipient_email', $email)
		->addFieldToFilter('is_abandoncart', $isabandoncart)
		->addFieldToFilter('rule_id', $ruleId)
		->addFieldToFilter('status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_SENT)
		->addFieldToFilter('emailtemplate_id', $templateEmailId);		
		$query->load();				
		return $query;
	}
	
	public function addFieldToFilter($attribute, $condition=null)
    {		
    	if($attribute=='status') $attribute = 'main_table.'.$attribute;
    	return parent::addFieldToFilter($attribute, $condition);
    }
}