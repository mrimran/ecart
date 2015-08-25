<?php
/**
 * @project: CartMigration
 * @author : LitExtension
 * @url    : http://litextension.com
 * @email  : litextension@gmail.com
 */

$installer = $this;
$installer->startSetup();
$installer->run("
    CREATE TABLE IF NOT EXISTS `{$this->getTable('lecamg/update')}`(
        `mage_id` int(11) unsigned not null,
        `domain` varchar(255),
        `id_import` int(11),
        FOREIGN KEY FR_CM_PRO(`mage_id`) REFERENCES `{$this->getTable('catalog/product')}`(`entity_id`) ON UPDATE CASCADE ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->endSetup();