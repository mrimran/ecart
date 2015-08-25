<?php

$installer = $this;

$resource = Mage::getSingleton('core/resource');

$tblFollowCoupon = $resource->getTableName('followupemail/coupons');

$tblFollowQueue = $resource->getTableName('followupemail/emailqueue');

$installer->startSetup();

$sql = "DROP TABLE IF EXISTS {$tblFollowCoupon};

	CREATE TABLE {$tblFollowCoupon} (

  `coupon_id` int(10) unsigned NOT NULL AUTO_INCREMENT,

  `rule_id` int(10) NOT NULL,

  `sale_rule_id` int(10) NOT NULL,

  `code` varchar(255) NOT NULL, 

  `use_customer` varchar(255) NOT NULL,

  `times_used` int(10),

  `expiration_date` datetime NULL,  

  `created_at` datetime NULL,

  `coupon_status` int(10) default 1,

  `coupon_type` int(10) default 1,

  PRIMARY KEY (`coupon_id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		";

$sql1 = "ALTER TABLE `{$tblFollowQueue}` 

		ADD `coupon_code` varchar(255) NOT NULL AFTER `sku`;

		";

$installer->run($sql1);

$installer->run($sql);

$installer->endSetup(); 

