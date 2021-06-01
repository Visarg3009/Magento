<?php
class Ccc_Order_Model_Order_Address extends Mage_Core_Model_Abstract
{
    const ADDRESS_TYPE_BILLING = 'billing';
    const ADDRESS_TYPE_SHIPPING = 'shipping';

    protected function _construct()
    {
        $this->_init('order/order_address');
    }
}
