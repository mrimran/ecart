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
    ALTER TABLE `{$this->getTable('lecamg/update')}` ADD `value` text;
");
$installer->endSetup();