<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');
$tblFollowRules = $resource->getTableName('followupemail/rules');
$tblFollowEmailQueue = $resource->getTableName('followupemail/emailqueue');
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$tblFollowRules};
CREATE TABLE {$tblFollowRules} (
  `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `from_date` varchar(10) NOT NULL,
  `to_date` varchar(10) NOT NULL,
  `event` varchar(255) NOT NULL,
  `cancel_event` text,
  `customer_group_ids` text,  
  `conditions_serialized` mediumtext,
  `copy_to_email` varchar(255) DEFAULT NULL,
  `send_mail_customer` int(10) DEFAULT 1,
  `only_newsletter_subscribers` int(10) DEFAULT 2,
  `is_active` int(10) DEFAULT 1,
  `sender_name` varchar(255) DEFAULT NULL,
  `sender_email` varchar(255) DEFAULT NULL,
  `email_chain` text NOT NULL,
  `store_ids` varchar(255) NOT NULL,
  `test_recipient` varchar(255) DEFAULT NULL,
  `test_customer_name` varchar(255) DEFAULT NULL,	
  `test_order_id` varchar(255) DEFAULT NULL,
  `send_button` text DEFAULT NULL,
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$tblFollowEmailQueue};
CREATE TABLE {$tblFollowEmailQueue} (
  `queue_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` int(10) DEFAULT 1,
  `create_date`  datetime NULL,
  `scheduled_at`  datetime NULL,
  `sent_at`  datetime NULL,
  `rule_id` int(10) NOT NULL,
  `order_id` int(10) NOT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `sender_email` varchar(255) DEFAULT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `recipient_email` varchar(255) DEFAULT NULL,
  `subject` varchar(255) default '',
  `content` text NOT NULL,
  `params` text NOT NULL,
  `emailtemplate_id` varchar(255) default '',
  `is_abandoncart` int(10) DEFAULT 0,
  `code` varchar(255) default '',
  `sku` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup(); 
