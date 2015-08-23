<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$tblFollowRules = $resource->getTableName('followupemail/rules');
$installer->startSetup();
$sql = "ALTER TABLE `{$tblFollowRules}` 
		ADD `coupon_status` int(10) DEFAULT 2 AFTER `store_ids` ,
		ADD `coupon_sales_rule_id` INT UNSIGNED DEFAULT NULL AFTER `coupon_status`,		
		ADD `coupon_prefix`  varchar(255) DEFAULT NULL AFTER `coupon_sales_rule_id` ,
		ADD `coupon_expire_days`  INT UNSIGNED DEFAULT NULL AFTER `coupon_prefix`;
		";
$installer->run($sql);
$installer->endSetup(); 
