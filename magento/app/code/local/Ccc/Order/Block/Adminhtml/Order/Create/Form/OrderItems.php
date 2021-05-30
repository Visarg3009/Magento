<?php
class Ccc_Order_Block_Adminhtml_Order_Create_Form_OrderItems extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $cart = null;

    public function __construct()
    {
        $this->_blockGroup = 'order';
        $this->_controller = 'adminhtml_order_create_form_orderItems';
        $this->_headerText = Mage::helper('order')->__('Items Ordered');
        parent::__construct();
        $this->_removeButton('add');
    }

    public function getUpdateUrl()
    {
        return $this->getUrl('*/adminhtml_order_create/updateCart');
    }

    public function setCart(Ccc_Order_Model_Cart $cart)
    {
        $this->cart = $cart;
        return $this;
    }

    public function getCart()
    {
        if (!$this->cart) {
            Mage::throwException(Mage::helper('order')->__('Cart Is not set.'));
        }
        return $this->cart;
    }
}
