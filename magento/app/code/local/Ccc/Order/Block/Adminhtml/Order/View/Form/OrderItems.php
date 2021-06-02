<?php
class Ccc_Order_Block_Adminhtml_Order_View_Form_OrderItems extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $order = null;

    public function __construct()
    {
        $this->_blockGroup = 'order';
        $this->_controller = 'adminhtml_order_view_form_orderItems';
        $this->_headerText = Mage::helper('order')->__('Items Ordered');
        parent::__construct();
        $this->_removeButton('add');
    }

    public function getUpdateUrl()
    {
        //return $this->getUrl('*/adminhtml_order_create/updateCart');
    }

    public function setOrder(Ccc_Order_Model_Order $order)
    {
        $this->order = $order;
        return $this;
    }

    public function getOrder()
    {
        if (!$this->order) {
            Mage::throwException(Mage::helper('order')->__('Order Is not set.'));
        }
        return $this->order;
    }
}