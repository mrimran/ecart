<?php
class MW_FollowUpEmail_Model_System_Config_Response
{
	const QUEUE_NOT_SENT_YET        	= 0;
    const QUEUE_STATUS_UNREAD        	= 1;
    const QUEUE_STATUS_READ         	= 2;
    const QUEUE_STATUS_CLICKED       	= 3;
    const QUEUE_STATUS_PURCHASED    	= 4;
    public static function toOptionArray()
    {
        return array(
			self::QUEUE_NOT_SENT_YET   => "Not Sent Yet", 
            self::QUEUE_STATUS_UNREAD  => 'Unread',
            self::QUEUE_STATUS_READ   => 'Read',
            self::QUEUE_STATUS_CLICKED => 'Clicked',
            self::QUEUE_STATUS_PURCHASED => 'Purchased',
        );
    }
}