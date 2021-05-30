<?php
class Ccc_Order_Block_Adminhtml_Order_Create_Form_AccountInfo extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $cart = null;

    public function __construct()
    {
        $this->_blockGroup = 'order';
        $this->_controller = 'adminhtml_order_create_form_accountInfo';
        $this->_headerText = Mage::helper('order')->__('Account Information');
        parent::__construct();
        $this->_removeButton('save');
        $this->_removeButton('back');
        $this->_removeButton('reset');
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
