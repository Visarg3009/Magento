<?php
class Ccc_Order_Model_Cart extends Mage_Core_Model_Abstract
{
    protected $customer = null;
    protected $items = null;
    protected $billingAddress = null;
    protected $shippingAddress = null;

    protected function _construct()
    {
        $this->_init('order/cart');
    }

    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->customer = $customer;
        return $this;
    }

    public function getCustomer()
    {
        if ($this->customer) {
            return $this->customer;
        }
        if (!$this->getCustomerId()) {
            return false;
        }
        $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
        $this->setCustomer($customer);
        return $this->customer;
    }

    public function setItems(Ccc_Order_Model_Resource_Cart_Item_Collection $items)
    {
        $this->items = $items;
        return $this;
    }

    public function getItems()
    {
        if ($this->items) {
            return $this->items;
        }
        if (!$this->getId()) {
            return false;
        }

        $items = Mage::getModel('order/cart_item')->getCollection()
            ->addFieldToFilter('cart_id', ['eq' => $this->getId()]);
        if ($items) {
            $this->setItems($items);
        }
        return $this->items;
    }

    public function getBillingAddress()
    {
        if ($this->billingAddress) {
            return $this->billingAddress;
        }
        if (!$this->getId()) {
            return false;
        }
        $collection = Mage::getModel('order/cart_address')->getCollection()
            ->addFieldToFilter('cart_id', ['eq' => $this->getId()])
            ->addFieldToFilter('address_type', ['eq' => Ccc_Order_Model_Cart_Address::ADDRESS_TYPE_BILLING]);
        $billingAddress = $collection->getFirstItem();

        $this->setBillingAddress($billingAddress);
        return $this->billingAddress;
    }

    public function setBillingAddress(Ccc_Order_Model_Cart_Address $address)
    {
        $this->billingAddress = $address;
        return $this;
    }

    public function getShippingAddress()
    {
        if ($this->shippingAddress) {
            return $this->shippingAddress;
        }
        if (!$this->getId()) {
            return false;
        }
        $collection = Mage::getModel('order/cart_address')->getCollection()
            ->addFieldToFilter('cart_id', ['eq' => $this->getId()])
            ->addFieldToFilter('address_type', ['eq' => Ccc_Order_Model_Cart_Address::ADDRESS_TYPE_SHIPPING]);
        $shippingAddress = $collection->getFirstItem();

        $this->setShippingAddress($shippingAddress);
        return $this->shippingAddress;
    }

    public function setShippingAddress(Ccc_Order_Model_Cart_Address $address)
    {
        $this->shippingAddress = $address;
        return $this;
    }

    public function addItemToCart(Mage_Catalog_Model_Product $product, $quantity = 1)
    {
        $collection = Mage::getModel('order/cart_item')->getCollection()
            ->addFieldToFilter('cart_id', ['eq' => $this->getId()])
            ->addFieldToFilter('product_id', ['eq' => $product->getId()]);
        $item = $collection->getFirstItem();

        if ($item->getData()) {
            $item->setQuantity($item->getQuantity() + $quantity);

            if ($product->getPrice() != $item->getBasePrice()) {
                $item->setBasePrice($product->getPrice())
                    ->setPrice($product->getPrice());
            }
            $item->save();
            return true;
        }
        $item = Mage::getModel('order/cart_item');
        $item->setCartId($this->getId());
        $item->setSku($product->getSku());
        $item->setName($product->getName());
        $item->setProductId($product->getId());
        $item->setBasePrice($product->getPrice());
        $item->setPrice($product->getPrice());
        $item->setQuantity($quantity);
        $item->setCreatedAt(time());
        $item->save();
        return true;
    }

    public function getAddresses()
    {
        if (!$this->cartId) {
            return false;
        }
        return Mage::getModel('order/cart_address')->getCollection()
            ->addFieldToFilter('cart_id', ['eq' => $this->getId()]);
    }
}
