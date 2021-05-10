<?php

$this->startSetup();

$this->addEntityType(Ccc_Seller_Model_Resource_Seller::ENTITY, [
    'entity_model'                => 'seller/seller',
    'attribute_model'             => 'seller/attribute',
    'table'                       => 'seller/seller',
    'increment_per_store'         => '0',
    'additional_attribute_table'  => 'seller/eav_attribute',
    'entity_attribute_collection' => 'seller/seller_attribute_collection',
]);

$this->createEntityTables('seller');
$this->installEntities();

$default_attribute_set_id = Mage::getModel('eav/entity_setup', 'core_setup')
    ->getAttributeSetId('seller', 'Default');

$this->run("UPDATE `eav_entity_type` SET `default_attribute_set_id` = {$default_attribute_set_id} WHERE `entity_type_code` = 'seller'");

$this->endSetup();
