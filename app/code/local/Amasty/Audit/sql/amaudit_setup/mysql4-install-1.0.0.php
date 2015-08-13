<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */  
$installer = $this;
$this->startSetup();

$installer->run("
   CREATE TABLE IF NOT EXISTS `{$this->getTable('amaudit/data')}` (
    `entity_id` int(11) NOT NULL AUTO_INCREMENT,  
	`date_time` datetime NOT NULL,  
	`username` varchar(80) ,  
	`name` varchar(80) ,  
	`ip` varchar(20) ,  
	`status` tinyint(4) NOT NULL,  
	PRIMARY KEY (`entity_id`)
   ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
");

$installer->run("
   CREATE TABLE IF NOT EXISTS `{$this->getTable('amaudit/lock')}`(  
   `entity_id` int(11) NOT NULL AUTO_INCREMENT,  
   `user_id` int(11) NOT NULL,  
   `count` smallint(6) NOT NULL, 
   `time_lock` text, 
   PRIMARY KEY (`entity_id`)
   ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
");

$installer->run("
   CREATE TABLE IF NOT EXISTS `{$this->getTable('amaudit/log')}`(  
      `entity_id` int(11) NOT NULL AUTO_INCREMENT,  
      `date_time` datetime NOT NULL,  
      `username` text NOT NULL,  
      `type` varchar(30) NOT NULL,  
      `category` varchar(100) NOT NULL, 
      `category_name` text,  
      `parametr_name` text,  
      `element_id` int(11) DEFAULT NULL,  
      `info` text,
      `store_id` mediumint(9) NOT NULL,  
      PRIMARY KEY (`entity_id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
");

$installer->run("
   CREATE TABLE IF NOT EXISTS `{$this->getTable('amaudit/log_details')}`(  
      `entity_id` int(11) NOT NULL AUTO_INCREMENT,
      `log_id` int(11) NOT NULL,
       `name` text NOT NULL,
       `old_value` text,
       `new_value` text,
       `model` text,
        PRIMARY KEY (`entity_id`),
        KEY `IDX_AMASTY_AMAUDIT_DETAILS_LOG_ID` (`log_id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
");

$installer->run("
  ALTER TABLE `{$this->getTable('amaudit/log_details')}`
    ADD CONSTRAINT `FK_AMASTY_AUDIT_LOG_DETAILS_LOG_ID_AMASTY_AUDIT_LOG_ENTINTY_ID` 
    FOREIGN KEY (`log_id`) REFERENCES `{$this->getTable('amaudit/log')}` (`entity_id`) ON DELETE CASCADE;
");

$this->endSetup();    



