<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */

$this->startSetup();


$this->run("
    CREATE TABLE IF NOT EXISTS `{$this->getTable('amaudit/visit')}` (
		`visit_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `username` varchar(80) NOT NULL,
		  `name` varchar(80) NOT NULL,
		  `session_start` datetime,
		  `session_end` datetime,
		  `ip` varchar(20) NOT NULL,
		  `location` varchar(100),
		  `session_id` varchar(255) NOT NULL,
		  PRIMARY KEY (`visit_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$this->run("
   CREATE TABLE IF NOT EXISTS `{$this->getTable('amaudit/visit_detail')}` (
		`detail_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `page_name` varchar(255) NOT NULL,
		  `page_url` varchar(255) NOT NULL,
		  `stay_duration` int(10) unsigned NOT NULL,
		  `session_id` varchar(255) NOT NULL,
		  PRIMARY KEY (`detail_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$this->run("

UPDATE `{$this->getTable('core/config_data')}`
			   SET path = 'amaudit/login/enableLock'
			   WHERE path = 'amaudit/general/enableLock';

UPDATE `{$this->getTable('core/config_data')}`
			   SET path = 'amaudit/login/numberFailed'
			   WHERE path = 'amaudit/general/numberFailed';

UPDATE `{$this->getTable('core/config_data')}`
			   SET path = 'amaudit/login/time'
			   WHERE path = 'amaudit/general/time';

UPDATE `{$this->getTable('core/config_data')}`
			   SET path = 'amaudit/login/run'
			   WHERE path = 'amaudit/general/run';

UPDATE `{$this->getTable('core/config_data')}`
			   SET path = 'amaudit/log/is_all_admins'
			   WHERE path = 'amaudit/general/is_all_admins';

UPDATE `{$this->getTable('core/config_data')}`
			   SET path = 'amaudit/log/log_users'
			   WHERE path = 'amaudit/general/log_users';

UPDATE `{$this->getTable('core/config_data')}`
			   SET path = 'amaudit/log/enableVisitHistory'
			   WHERE path = 'amaudit/general/enableVisitHistory';

UPDATE `{$this->getTable('core/config_data')}`
			   SET path = 'amaudit/log/delete_logs_afret_days'
			   WHERE path = 'amaudit/general/delete_logs_afret_days';

UPDATE `{$this->getTable('core/config_data')}`
			   SET path = 'amaudit/log/delete_pages_history_after_days'
			   WHERE path = 'amaudit/general/delete_pages_history_after_days';

UPDATE `{$this->getTable('core/config_data')}`
			   SET path = 'amaudit/log/delete_login_attempts_after_days'
			   WHERE path = 'amaudit/general/delete_login_attempts_after_days';

");

$this->endSetup();
