<?php
class Ccc_Order_Model_Cart_Item extends Mage_Core_Model_Abstract
{
    protected $cart = null;
    protected $product = null;

    protected function _construct()
    {
        $this->_init('order/cart_item');
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

    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        $this->product = $product;
        return $this;
    }

    public function getProduct()
    {
        if ($this->product) {
            return $this->product;
        }
        if (!$this->getProductId()) {
            return false;
        }
        $product = Mage::getModel('catalog/product')->load($this->getProductId());
        $this->setProduct($product);
        return $this->product;
    }
}
