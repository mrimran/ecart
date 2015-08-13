<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */  
$this->startSetup();

$tableName = Mage::getSingleton('core/resource')->getTableName('productalert/stock');
$dbname = (string)Mage::getConfig()->getNode('global/resources/default_setup/connection/dbname');
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
    ALTER TABLE `{$tableName}` ADD COLUMN `email` VARCHAR( 254 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
"); 
}
try{
    $keySql = 'SHOW INDEX FROM '.$tableName." where column_name = 'customer_id'";
    $keys = $this->getConnection()->fetchAll($keySql);

    foreach($keys  as $key => $keyName){
        if(array_key_exists('Key_name', $keyName)) {
		$this->run("
         	   ALTER TABLE `{$tableName}` DROP INDEX `{$keyName['Key_name']}`;
        	");
    		}
 
	} 
}
catch(Exception $exc){
	Mage::log($exc->getMessage());
}  
try{
    $keySql = "SELECT CONSTRAINT_NAME FROM information_schema.key_column_usage WHERE referenced_table_name IS NOT NULL AND table_name = '" . $tableName ."'  AND column_name = 'customer_id' AND TABLE_SCHEMA = '".$dbname ."'";
    $keys = $this->getConnection()->fetchCol($keySql);

    foreach($keys as $keyName){
	 $this->run("
            ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$keyName}`;
        ");
    }

    
}
catch(Exception $exc){
	Mage::log($exc->getMessage());
}

$this->endSetup();         
 