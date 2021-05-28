<?php
class Ccc_Order_Block_Adminhtml_Order_Create_Form_ShipmentMethod_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('order_create_form_shipmentMethod_grid');
        //$this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    public function getShippingMethodTitle()
    {
        return $methods = Mage::getModel('shipping/config')->getActiveCarriers();
    }

    public function getCart()
    {
        return Mage::registry('order_cart');
    }
}
