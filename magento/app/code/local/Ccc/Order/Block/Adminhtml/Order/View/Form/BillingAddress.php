<?php
class Ccc_Order_Block_Adminhtml_Order_View_Form_BillingAddress extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $order = null;

    public function __construct()
    {
        $this->_blockGroup = 'order';
        $this->_controller = 'adminhtml_order_view_form_billingAddress';
        $this->_headerText = Mage::helper('order')->__('Billing Address');
        parent::__construct();
        $this->_removeButton('back');
        $this->_removeButton('reset');
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
