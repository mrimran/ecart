<?php
/**
 * @category   Cactimedia
 * @package    Cactimedia_Cybersourcesa
 * @author     magepsycho@gmail.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('cybersourcesa_api_debug')};
CREATE TABLE {$this->getTable('cybersourcesa_api_debug')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `created_time` datetime NULL,
  `request_body` text,
  `response_body` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();