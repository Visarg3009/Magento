<?php
class Ccc_Vendor_Model_Resource_Product extends Mage_Eav_Model_Entity_Abstract
{
    const ENTITY = 'vendor_product';

    public function __construct()
    {
        $this->setType(self::ENTITY)
            ->setConnection('core_read', 'core_write');

        parent::__construct();
    }

    public function loadBySku($sku)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array('product_sku' => $sku);
        $select  = $adapter->select()
            ->from($this->getEntityTable() . '_varchar', array($this->getEntityIdField()))
            ->where('value = :product_sku');


        $productId = $adapter->fetchOne($select, $bind);
        if ($productId) {
            return true;
        }
        return false;
    }
}
