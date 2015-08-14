<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS `{$installer->getTable('amasty_audit_location')}` (
	`location_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`geoip_loc_id` INT(10) UNSIGNED NOT NULL,
	`country` CHAR(2) NULL DEFAULT NULL,
	`region` CHAR(2) NULL DEFAULT NULL,
	`city` VARCHAR(255) NULL DEFAULT NULL,
	`postal_code` CHAR(5) NULL DEFAULT NULL,
	`latitude` FLOAT NULL DEFAULT NULL,
	`longitude` FLOAT NULL DEFAULT NULL,
	`dma_code` INT(11) NULL DEFAULT NULL,
	`area_code` INT(11) NULL DEFAULT NULL,
	PRIMARY KEY (`location_id`),
	INDEX `geoip_loc_id` (`geoip_loc_id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
AUTO_INCREMENT=440001
;

CREATE TABLE IF NOT EXISTS `{$installer->getTable('amasty_audit_block')}` (
	`block_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`start_ip_num` INT(10) UNSIGNED NOT NULL,
	`end_ip_num` INT(10) UNSIGNED NOT NULL,
	`geoip_loc_id` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`block_id`),
	INDEX `start_ip_num` (`start_ip_num`),
	INDEX `end_ip_num` (`end_ip_num`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
AUTO_INCREMENT=1800001
;


");
$installer->endSetup();
