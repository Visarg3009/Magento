<?php
class Ccc_Order_Block_Adminhtml_Order_View_Form_ShipmentMethod_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $cart = null;

    public function __construct()
    {
        parent::__construct();
        $this->setId('order_create_form_shipmentMethod_grid');
        //$this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
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

    public function getShippingMethodTitle()
    {
        return $methods = Mage::getModel('shipping/config')->getActiveCarriers();
    }
}
