<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'tmcore/module'
 */
$typeText = defined('Varien_Db_Ddl_Table::TYPE_TEXT')
    ? Varien_Db_Ddl_Table::TYPE_TEXT : Varien_Db_Ddl_Table::TYPE_VARCHAR;

$table = $installer->getConnection()
    ->newTable($installer->getTable('tmcore/module'))
    ->addColumn('code', $typeText, 50, array(
            'nullable'  => false,
            'primary'   => true,
        )
    )
//    ->addColumn('version', Varien_Db_Ddl_Table::TYPE_TEXT, 50)
    ->addColumn('data_version', $typeText, 50)
    ->addColumn('license_key', $typeText, 32);
$installer->getConnection()->createTable($table);

$installer->endSetup();
