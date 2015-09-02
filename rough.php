<?php
require_once 'app/Mage.php';
$defaultStoreId = Mage::app()->getWebsite(true)->getDefaultGroup()->getDefaultStoreId();
echo $defaultStoreId;