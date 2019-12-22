<?php
  
$installer = $this;
  
$installer->startSetup();
  
$installer->run("
  
-- DROP TABLE IF EXISTS {$this->getTable('partners')};
CREATE TABLE {$this->getTable('partners')} (
  `partners_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`partners_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
    ");

$installer->getConnection()->addColumn(
    $this->getTable('sales/order'),
    'partner_name',
    'varchar(255) DEFAULT NULL'
);

$installer->getConnection()->addColumn(
    $this->getTable('sales/order_grid'),
    'partner_name',
    'varchar(255) DEFAULT NULL'
);

$installer->endSetup(); 
