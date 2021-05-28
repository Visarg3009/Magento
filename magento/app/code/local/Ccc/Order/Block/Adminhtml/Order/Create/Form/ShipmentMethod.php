<?php
class Ccc_Order_Block_Adminhtml_Order_Create_Form_ShipmentMethod extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'order';
        $this->_controller = 'adminhtml_order_create_form_shipmentMethod';
        $this->_headerText = Mage::helper('order')->__('Shipment Method');
        parent::__construct();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/adminhtml_order_create/index');
    }
}
