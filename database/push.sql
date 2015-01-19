

CREATE TABLE `customer_to_device` (
  `customer_id` int(11) NOT NULL,
  `device_token` varchar(255) NOT NULL,
  `os` varchar(45) NOT NULL,
  `device_type` varchar(20) NOT NULL,
  `version` varchar(45) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`customer_id`,`device_token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `task` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `trigger` text NOT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `customer_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL DEFAULT '0',
  `customer_id` int(11) NOT NULL DEFAULT '0',  
  `email` varchar(96) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `option` text NOT NULL,
  `date_added` datetime NOT NULL,  
  PRIMARY KEY (`log_id`),
  KEY `customer_id` (`customer_id`),
  KEY `email` (`email`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;