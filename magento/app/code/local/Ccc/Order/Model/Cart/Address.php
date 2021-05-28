<?php
class Ccc_Order_Model_Cart_Address extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('order/cart_address');
    }

    public function getShippingAddress($cartId = null, $customerId = null)
    {
        $collection = Mage::getModel('order/cart_address')->getCollection();
        $collection->getSelect()
            ->where('cart_id = ?', $cartId)
            ->Where('address_type = ?', 'shipping');

        $cartAddress = $collection->getFirstItem();
        if ($cartAddress->getData() == null) {
            $shippingAddress = Mage::getModel('customer/customer')->load($customerId)->getDefaultShippingAddress();
            $cartAddress = Mage::getModel('order/cart_address');
            $cartAddress->setCartId($cartId);
            $cartAddress->setAddressType('shipping');
            if ($shippingAddress) {
                $cartAddress->setName($shippingAddress->getName());
                $cartAddress->setAddress($shippingAddress->getStreet()[0]);
                $cartAddress->setCity($shippingAddress->getCity());
                $cartAddress->setState($shippingAddress->getRegion());
                $cartAddress->setCountry($shippingAddress->getCountryId());
                $cartAddress->setZipcode($shippingAddress->getPostcode());
            }
            $cartAddress->save();
        }
        Mage::register('shipping_data', $cartAddress);
        return $cartAddress;
    }

    public function getBillingAddress($cartId = null, $customerId = null)
    {
        $collection = Mage::getModel('order/cart_address')->getCollection();
        $collection->getSelect()
            ->where('cart_id = ?', $cartId)
            ->Where('address_type = ?', 'billing');

        $cartAddress = $collection->getFirstItem();
        if ($cartAddress->getData() == null) {
            $billingAddress = Mage::getModel('customer/customer')->load($customerId)->getDefaultBillingAddress();
            $cartAddress = Mage::getModel('order/cart_address');
            $cartAddress->setCartId($cartId);
            $cartAddress->setAddressType('billing');
            if ($billingAddress) {
                $cartAddress->setName($billingAddress->getName());
                $cartAddress->setAddress($billingAddress->getStreet()[0]);
                $cartAddress->setCity($billingAddress->getCity());
                $cartAddress->setState($billingAddress->getRegion());
                $cartAddress->setCountry($billingAddress->getCountryId());
                $cartAddress->setZipcode($billingAddress->getPostcode());
            }
            $cartAddress->save();
        }
        Mage::register('billing_data', $cartAddress);
        return $cartAddress;
    }
}
