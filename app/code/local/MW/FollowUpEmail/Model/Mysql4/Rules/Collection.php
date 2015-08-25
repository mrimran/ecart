<?php

class MW_FollowUpEmail_Model_Mysql4_Rules_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract

{

    public function _construct()

    {

        parent::_construct();

        $this->_init('followupemail/rules');

    }

	

	public function loadRulesByEvent($event = "",$storeId,$groupId)

	{

		$now = date('Y-m-d H:i:s',Mage::getModel('core/date')->timestamp(time()));

		$query = $this->addFieldToFilter('is_active', 1)			

			/*->addFieldToFilter('from_date', array('to' => $now))

			->addFieldToFilter('to_date', array('from' => $now))*/

			->addFieldToFilter('event', $event);

		$query->getSelect()->where('find_in_set(?, store_ids) or store_ids = 0', (int)$storeId);
		if($groupId !="")
        $query->getSelect()->where('find_in_set(?, customer_group_ids)', (int)$groupId);

		$query->getSelect()->where('from_date is null or from_date="0000-00-00" or from_date="" or from_date<=?', $now);

		$query->getSelect()->where('to_date is null or to_date="0000-00-00" or to_date="" or to_date>=?', $now);		

		$query->load();		
		
		return $query;

	}

	

	public function loadRulesByCanecelEvent($cancelEvent = "",$storeId,$groupId)

	{

		$now = date('Y-m-d H:i:s',Mage::getModel('core/date')->timestamp(time()));

		$query = $this->addFieldToFilter('is_active', 1)			

			/*->addFieldToFilter('from_date', array('to' => $now))

			->addFieldToFilter('to_date', array('from' => $now))*/

			//->addFieldToFilter('cancel_event', $cancelEvent);

			->addFieldToFilter('cancel_event',array('like'=>'%'.$cancelEvent.'%'));

		$query->getSelect()->where('find_in_set(?, store_ids) or store_ids = 0', (int)$storeId);

        $query->getSelect()->where('find_in_set(?, customer_group_ids)', (int)$groupId);

		$query->getSelect()->where('from_date is null or from_date="0000-00-00" or from_date="" or from_date<=?', $now);

		$query->getSelect()->where('to_date is null or to_date="0000-00-00" or to_date="" or to_date>=?', $now);						

		$query->load();		

		return $query;

	}

}