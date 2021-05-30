<?php
class Ccc_Order_Block_Adminhtml_Order_Create_Form_OrderTotal extends Mage_Adminhtml_Block_Template
{
    protected $cart = null;

    public function __construct()
    {
        parent::__construct();
        $this->setId('adminhtml_order_create_form_orderTotal');
    }

    public function getHeaderText()
    {
        return Mage::helper('order')->__('Order Total');
    }

    public function getHeader()
    {
        return Mage::helper('order')->__('Order Total');
    }

    public function getButtonsHtml()
    {
        $addButtonData = array(
            'label'     => Mage::helper('order')->__('Submit Order'),
            'onclick'   => 'order.setCustomerId(false)',
            'class'     => 'add',
        );
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
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
