<?php
class Ccc_Order_Model_Order extends Mage_Core_Model_Abstract
{
    protected $items = null;

    protected function _construct()
    {
        $this->_init('order/order');
    }

    public function setItems(Ccc_Order_Model_Resource_Order_Item_Collection $items)
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

        $items = Mage::getModel('order/order_item')->getCollection()
            ->addFieldToFilter('order_id', ['eq' => $this->getId()]);
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
        $collection = Mage::getModel('order/order_address')->getCollection()
            ->addFieldToFilter('order_id', ['eq' => $this->getId()])
            ->addFieldToFilter('address_type', ['eq' => Ccc_Order_Model_Order_Address::ADDRESS_TYPE_BILLING]);
        $billingAddress = $collection->getFirstItem();

        $this->setBillingAddress($billingAddress);
        return $this->billingAddress;
    }

    public function setBillingAddress(Ccc_Order_Model_Order_Address $address)
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
        $collection = Mage::getModel('order/order_address')->getCollection()
            ->addFieldToFilter('order_id', ['eq' => $this->getId()])
            ->addFieldToFilter('address_type', ['eq' => Ccc_Order_Model_Order_Address::ADDRESS_TYPE_SHIPPING]);
        $shippingAddress = $collection->getFirstItem();

        $this->setShippingAddress($shippingAddress);
        return $this->shippingAddress;
    }

    public function setShippingAddress(Ccc_Order_Model_Order_Address $address)
    {
        $this->shippingAddress = $address;
        return $this;
    }
}
