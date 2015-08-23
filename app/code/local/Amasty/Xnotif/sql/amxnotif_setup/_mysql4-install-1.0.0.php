<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */  
$this->startSetup();
$tableName = Mage::getSingleton('core/resource')->getTableName('productalert/stock');
$fieldsSql = 'SHOW COLUMNS FROM ' . $tableName;
$cols = $this->getConnection()->fetchCol($fieldsSql);

if (!in_array('parent_id', $cols))
{
    $this->run("
        ALTER TABLE `{$tableName}` ADD COLUMN `parent_id` INT NULL;
    ");
}  

if (!in_array('email', $cols))
{
    $this->run("
        ALTER TABLE `{$tableName}` ADD COLUMN `email` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
    "); 
}

$keySql = 'SHOW INDEX FROM '.$tableName." where column_name = 'customer_id'";
$keys = $this->getConnection()->fetchCol($keySql);
foreach($keys['key_name'] as $keyName){
     $this->run("
        ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$keyName}`;
    ");
}

$this->endSetup();        
 