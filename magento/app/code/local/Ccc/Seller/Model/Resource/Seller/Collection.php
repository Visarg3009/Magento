<?php
class Ccc_Seller_Model_Resource_Seller_Collection extends Mage_Catalog_Model_Resource_Collection_Abstract
{
    public function __construct()
    {
        $this->setEntity('seller');
        parent::__construct();
    }
}
