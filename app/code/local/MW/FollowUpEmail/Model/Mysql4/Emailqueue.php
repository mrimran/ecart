<?php
class MW_FollowUpEmail_Model_Mysql4_Emailqueue extends Mage_Core_Model_Mysql4_Abstract
{
    const MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';

    public function _construct() 
    {
       $this->_init('followupemail/emailqueue', 'queue_id');
    }   	
	
	public function getIdByCode($code)
    {
        $db = $this->_getReadAdapter();

        $select = $db->select()
            ->from($this->getMainTable(), 'queue_id')
            ->where('code=?', $code)
            ->limit(1);

        return $db->fetchOne($select);
    }
}