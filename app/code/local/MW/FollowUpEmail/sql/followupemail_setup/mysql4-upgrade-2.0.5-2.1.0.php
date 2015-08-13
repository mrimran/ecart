<?php
$installer = $this;
$resource = Mage::getSingleton('core/resource');

$tablerules = $resource->getTableName('followupemail/rules');

$installer->startSetup();

$sql = " ALTER TABLE `{$tablerules}`
          ADD `campaign_source` varchar(255) DEFAULT  NULL,
          ADD `campaign_medium` varchar(255) DEFAULT  NULL,
          ADD `campaign_term` varchar(255) DEFAULT  NULL,
          ADD `campaign_content` varchar(255) DEFAULT  NULL,
          ADD `campaign_name` varchar(255) DEFAULT  NULL;
    ";

$installer->run($sql);
$installer->endSetup();