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
    CREATE TABLE IF NOT EXISTS `{$this->getTable('lecamg/import')}`(
        `domain` varchar(255),
        `type`  varchar(255),
        `id_import` int(11),
        `mage_id` int(11),
        `status` int(5),
        `value` text,
        INDEX (`domain`, `type`, `id_import`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE IF NOT EXISTS `{$this->getTable('lecamg/user')}`(
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) UNIQUE NOT NULL,
        `notice`  TEXT,
        PRIMARY KEY (`id`)
    )ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE IF NOT EXISTS `{$this->getTable('lecamg/recent')}`(
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `domain` VARCHAR(255) UNIQUE NOT NULL,
        `notice`  TEXT,
        PRIMARY KEY (`id`)
    )ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();