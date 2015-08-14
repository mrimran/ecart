<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$allTablesSql = 'SHOW TABLES';
$allTables = $installer->getConnection()->fetchCol($allTablesSql);

function renameTable($allTables, $inputTable, $outputTable, $installer)
{
	$inputTableName = Mage::getSingleton("core/resource")->getTableName($inputTable);
	$outputTableName = Mage::getSingleton("core/resource")->getTableName($outputTable);

	if (!in_array($outputTableName, $allTables)) {
		if (in_array($inputTableName, $allTables)) {
			$installer->run("
				RENAME TABLE `{$inputTableName}` TO `{$outputTableName}`
			");
		}
	}
}

$auditTableLocation = "amasty_audit_location";
$auditTableBlock = "amasty_audit_block";
$geoTableLocation = "amasty_geoip_location";
$geoTableBlock = "amasty_geoip_block";

renameTable($allTables, $auditTableLocation, $geoTableLocation, $installer);
renameTable($allTables, $auditTableBlock, $geoTableBlock, $installer);

$installer->run("

UPDATE `{$installer->getTable('core/config_data')}`
			   SET path = 'amgeoip/import/block'
			   WHERE path = 'amaudit/import/block';

UPDATE `{$installer->getTable('core/config_data')}`
			   SET path = 'amgeoip/import/location'
			   WHERE path = 'amaudit/import/location';

CREATE TABLE IF NOT EXISTS `{$installer->getTable('amaudit/active')}` (
	`entity_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`data_id` INT(10) UNSIGNED NOT NULL,
	`session_id` VARCHAR(255) NOT NULL,
	`recent_activity` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`entity_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=23
;


");
$installer->endSetup();
