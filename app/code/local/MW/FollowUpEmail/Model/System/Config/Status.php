<?php

class MW_FollowUpEmail_Model_System_Config_Status

{

    const QUEUE_STATUS_READY        = 1;

    const QUEUE_STATUS_SENT         = 2;

    const QUEUE_STATUS_FAILED       = 3;

    const QUEUE_STATUS_CANCELLED    = 4;
	



    public static function toOptionArray()
    {
        return array(
            self::QUEUE_STATUS_READY  => 'Pending',
            self::QUEUE_STATUS_SENT   => 'Sent',
            self::QUEUE_STATUS_FAILED => 'Failed',
            self::QUEUE_STATUS_CANCELLED => 'Cancelled',
        );
    }
}