<?php
class Ccc_Order_Block_Adminhtml_Order_Create_Form_BillingAddress extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'order';
        $this->_controller = 'adminhtml_order_create_form_billingAddress';
        $this->_headerText = Mage::helper('order')->__('Billing Address');
        parent::__construct();
        $this->_removeButton('back');
        $this->_removeButton('reset');
    }
}
