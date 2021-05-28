<?php
class Ccc_Order_Block_Adminhtml_Order_Create_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'order';
        $this->_controller = 'adminhtml_order_create_customer';
        $this->_headerText = Mage::helper('order')->__('Orders');
        $this->_removeButton('add');
        $this->_addBackButton('back');
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/adminhtml_order/index');
    }
}
