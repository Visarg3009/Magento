<?php
$installer = $this;

$installer->updateAttribute(Ccc_Vendor_Model_Product::ENTITY, 'price', 'frontend_input', 'price');
$installer->updateAttribute(Ccc_Vendor_Model_Product::ENTITY, 'price', 'backend_type', 'decimal');

$installer->endSetup();
