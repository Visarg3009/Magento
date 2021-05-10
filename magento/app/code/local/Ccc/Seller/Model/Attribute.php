<?php

class Ccc_Seller_Model_Attribute extends Mage_Eav_Model_Attribute
{
    const MODULE_NAME = 'Ccc_Seller';

    protected $_eventObject = 'attribute';

    protected function _construct()
    {
        $this->_init('seller/attribute');
    }
}
