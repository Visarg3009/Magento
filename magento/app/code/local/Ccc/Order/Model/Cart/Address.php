<?php
class Ccc_Order_Model_Cart_Address extends Mage_Core_Model_Abstract
{
    protected $cart = null;
    protected $customer = null;
    protected $customerBillingAddress = null;
    protected $customerShippingAddress = null;

    const ADDRESS_TYPE_BILLING = 'billing';
    const ADDRESS_TYPE_SHIPPING = 'shipping';

    protected function _construct()
    {
        $this->_init('order/cart_address');
    }

    public function setCart(Ccc_Order_Model_Cart $cart)
    {
        $this->cart = $cart;
        return $this;
    }

    public function getCart()
    {
        if ($this->cart) {
            return $this->cart;
        }
        if (!$this->getCartId()) {
            return false;
        }
        $cart = Mage::getModel('order/cart')->load($this->getCartId());
        $this->setCart($cart);
        return $this->cart;
    }

    public function setCustomerBillingAddress(Mage_Customer_Model_Address $address)
    {
        $this->customerBillingAddress = $address;
        return $this;
    }

    public function getCustomerBillingAddress()
    {
        if ($this->customerBillingAddress) {
            return $this->customerBillingAddress;
        }

        $address = Mage::getModel('customer/customer')->load($this->getCart()->getCustomerId())->getDefaultBillingAddress();
        if (!$address) {
            $address = Mage::getModel('customer/address');
        }
        $this->setCustomerBillingAddress($address);
        return $this->customerBillingAddress;
    }

    public function setCustomerShippingAddress(Mage_Customer_Model_Address $address)
    {
        $this->customerShippingAddress = $address;
        return $this;
    }

    public function getCustomerShippingAddress()
    {
        if ($this->customerShippingAddress) {
            return $this->customerShippingAddress;
        }

        $address = Mage::getModel('customer/customer')->load($this->getCart()->getCustomerId())->getDefaultShippingAddress();
        if (!$address) {
            $address = Mage::getModel('customer/address');
        }
        $this->setCustomerShippingAddress($address);
        return $this->customerShippingAddress;
    }
}
