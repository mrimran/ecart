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
// Loader email template
$model = Mage::getModel('followupemail/loadtemplate');
$model->loademailtemplate();
/*Follow Up - Abandoned Cart*/
$templateEmailIdAbandonedCart1 = $model->getIdTemplateByCode("Sample - Abandoned Cart #1");
$templateEmailIdAbandonedCart2 = $model->getIdTemplateByCode("Sample - Abandoned Cart #2");
$templateEmailIdAbandonedCart3 = $model->getIdTemplateByCode("Sample - Abandoned Cart #3");
$arr1 = array ( 
			array (
				'BEFORE' => 1,
				'DAYS' => 0,
				'HOURS' => 0,
				'MINUTES' => 10,
				'TEMPLATE_ID' => 'email:'.$templateEmailIdAbandonedCart1.'',
			),
			array (
				'BEFORE' =>1,
				'DAYS' => 1,
				'HOURS' => 0,
				'MINUTES' => 0,
				'TEMPLATE_ID' => 'email:'.$templateEmailIdAbandonedCart2.'',
			),
			array (
				'BEFORE' =>1,
				'DAYS' => 7,
				'HOURS' => 0,
				'MINUTES' => 0,
				'TEMPLATE_ID' => 'email:'.$templateEmailIdAbandonedCart3.'',
			)
		);
$serialize1 = serialize($arr1);
$sqlInsertRuleAbandonedCart = <<<EOD
	INSERT INTO `$tblFollowRules` (`rule_id`, `title`, `from_date`, `to_date`, `event`, `cancel_event`, `customer_group_ids`, `conditions_serialized`, `copy_to_email`, `send_mail_customer`, `only_newsletter_subscribers`, `is_active`, `sender_name`, `sender_email`, `email_chain`, `store_ids`, `test_recipient`, `test_customer_name`, `test_order_id`, `send_button`) VALUES
(1, 'Sample - Abandoned Cart', '', '', 'abandoned_cart_appeared', 'cart_updated,new_order_placed,', '0,1,2', 'a:6:{s:4:"type";s:54:"followupemail/followupemailrule_rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}', NULL, 1, 2, 2, NULL, NULL, '$serialize1', '0', NULL, NULL, NULL, NULL);
EOD;

/*Follow Up - Pending Order*/
$templateEmailIdPendingOrder1 = $model->getIdTemplateByCode("Sample - Pending Order #1");
$templateEmailIdPendingOrder2 = $model->getIdTemplateByCode("Sample - Pending Order #2");
$templateEmailIdPendingOrder3 = $model->getIdTemplateByCode("Sample - Pending Order #3");
$arr2 = array ( 
			array (
				'BEFORE' => 1,
				'DAYS' => 0,
				'HOURS' => 0,
				'MINUTES' => 10,
				'TEMPLATE_ID' => 'email:'.$templateEmailIdPendingOrder1.'',
			),
			array (
				'BEFORE' =>1,
				'DAYS' => 1,
				'HOURS' => 0,
				'MINUTES' => 0,
				'TEMPLATE_ID' => 'email:'.$templateEmailIdPendingOrder2.'',
			),
			array (
				'BEFORE' =>1,
				'DAYS' => 7,
				'HOURS' => 0,
				'MINUTES' => 0,
				'TEMPLATE_ID' => 'email:'.$templateEmailIdPendingOrder3.'',
			)
		);
$serialize2 = serialize($arr2);
$sqlInsertRulePendingOrder = <<<EOD
	INSERT INTO `$tblFollowRules` (`rule_id`, `title`, `from_date`, `to_date`, `event`, `cancel_event`, `customer_group_ids`, `conditions_serialized`, `copy_to_email`, `send_mail_customer`, `only_newsletter_subscribers`, `is_active`, `sender_name`, `sender_email`, `email_chain`, `store_ids`, `test_recipient`, `test_customer_name`, `test_order_id`, `send_button`) VALUES
(2, 'Sample - Pending Order', '', '', 'new_order_placed', 'order_status_complete,', '0,1,2', 'a:6:{s:4:"type";s:54:"followupemail/followupemailrule_rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}', NULL, 1, 2, 2, NULL, NULL, '$serialize2', '0', NULL, NULL, NULL, NULL);
EOD;

/*Follow Up - Completed Order*/
$templateEmailIdCompletedOrder1 = $model->getIdTemplateByCode("Sample - Completed Order #1");
$templateEmailIdCompletedOrder2 = $model->getIdTemplateByCode("Sample - Completed Order #2");
$arr3 = array ( 
			array (
				'BEFORE' => 1,
				'DAYS' => 7,
				'HOURS' => 0,
				'MINUTES' => 0,
				'TEMPLATE_ID' => 'email:'.$templateEmailIdCompletedOrder1.'',
			),
			array (
				'BEFORE' =>1,
				'DAYS' => 14,
				'HOURS' => 0,
				'MINUTES' => 0,
				'TEMPLATE_ID' => 'email:'.$templateEmailIdCompletedOrder2.'',
			)
		);
$serialize3 = serialize($arr3);
$sqlInsertRuleCompletedOrder = <<<EOD
	INSERT INTO `$tblFollowRules` (`rule_id`, `title`, `from_date`, `to_date`, `event`, `cancel_event`, `customer_group_ids`, `conditions_serialized`, `copy_to_email`, `send_mail_customer`, `only_newsletter_subscribers`, `is_active`, `sender_name`, `sender_email`, `email_chain`, `store_ids`, `test_recipient`, `test_customer_name`, `test_order_id`, `send_button`) VALUES
(3, 'Sapmle - Completed Order', '', '', 'order_status_complete', 'order_status_closed,', '0,1,2', 'a:6:{s:4:"type";s:54:"followupemail/followupemailrule_rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}', NULL, 1, 2, 2, NULL, NULL, '$serialize3', '0', NULL, NULL, NULL, NULL);
EOD;

/*Follow Up - New Account*/
$templateEmailIdNewAccount1 = $model->getIdTemplateByCode("Sample - New Account #1");
$templateEmailIdNewAccount2 = $model->getIdTemplateByCode("Sample - New Account #2");
$arr4 = array ( 
			array (
				'BEFORE' => 1,
				'DAYS' => 30,
				'HOURS' => 0,
				'MINUTES' => 0,
				'TEMPLATE_ID' => 'email:'.$templateEmailIdNewAccount1.'',
			),
			array (
				'BEFORE' =>1,
				'DAYS' => 60,
				'HOURS' => 0,
				'MINUTES' => 0,
				'TEMPLATE_ID' => 'email:'.$templateEmailIdNewAccount2.'',
			)
		);
$serialize4 = serialize($arr4);
$sqlInsertRuleNewAccount = <<<EOD
	INSERT INTO `$tblFollowRules` (`rule_id`, `title`, `from_date`, `to_date`, `event`, `cancel_event`, `customer_group_ids`, `conditions_serialized`, `copy_to_email`, `send_mail_customer`, `only_newsletter_subscribers`, `is_active`, `sender_name`, `sender_email`, `email_chain`, `store_ids`, `test_recipient`, `test_customer_name`, `test_order_id`, `send_button`) VALUES
(4, 'Sample - New Account', '', '', 'new_customer_signed_up', 'new_order_placed,', '0,1,2', 'a:6:{s:4:"type";s:54:"followupemail/followupemailrule_rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";}', NULL, 1, 2, 2, NULL, NULL, '$serialize4', '0', NULL, NULL, NULL, NULL);
EOD;

$installer->run($sqlInsertRuleAbandonedCart);
$installer->run($sqlInsertRulePendingOrder);
$installer->run($sqlInsertRuleCompletedOrder);
$installer->run($sqlInsertRuleNewAccount);
$installer->endSetup(); 
