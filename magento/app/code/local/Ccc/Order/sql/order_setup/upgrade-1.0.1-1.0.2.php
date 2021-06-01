<?php
$installer = $this;

$query = "ALTER TABLE `order_item` ADD COLUMN sku VARCHAR (50) COMMENT 'SKU';";
$installer->getConnection()->query($query);

$query = "ALTER TABLE `order_item` ADD COLUMN name VARCHAR (50) COMMENT 'Name';";
$installer->getConnection()->query($query);

$installer->endSetup();
