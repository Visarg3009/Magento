<?php
$installer = $this;

$query = "ALTER TABLE `order_address` ADD COLUMN name VARCHAR (50) COMMENT 'Name';";
$installer->getConnection()->query($query);

$installer->endSetup();
