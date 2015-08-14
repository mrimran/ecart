<?php

$installer = $this;

$resource = Mage::getSingleton('core/resource');

$tblFollowQueue = $resource->getTableName('followupemail/emailqueue');

$installer->startSetup();

$sql = "ALTER TABLE `{$tblFollowQueue}` 

		ADD `customer_response` int(10) DEFAULT 0;

		";

$installer->run($sql);

$installer->endSetup(); 

