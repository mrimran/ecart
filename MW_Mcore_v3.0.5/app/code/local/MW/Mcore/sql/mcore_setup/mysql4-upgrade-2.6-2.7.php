<?php
$installer = $this;
$installer->startSetup();
$resource =Mage::getSingleton('core/resource');
$installer->run("

DROP TABLE IF EXISTS {$resource->getTableName('mcore/notification')};

CREATE TABLE {$resource->getTableName('mcore/notification')} (
  `notification_id` int(11) unsigned NOT NULL auto_increment,
  `type` varchar(25) NOT NULL default '', 
  `message` text NOT NULL default '',
  `time_apply` datetime NULL,
  `status` smallint(6) NOT NULL default '0',   
  `message_id` int(11),
  `extension_key` varchar(25) NULL,
  `current_display` smallint(6) NOT NULL default '0',
  
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

Mage::getModel('core/config')->saveConfig("mcore/upgraded",1); 
$installer->endSetup(); 