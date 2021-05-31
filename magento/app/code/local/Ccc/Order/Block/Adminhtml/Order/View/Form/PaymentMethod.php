<?php
class Ccc_Order_Block_Adminhtml_Order_View_Form_PaymentMethod extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'order';
        $this->_controller = 'adminhtml_order_view_form_paymentMethod';
        $this->_headerText = Mage::helper('order')->__('Payment Method');
        parent::__construct();
        $this->_removeButton('add');
    }

    public function getViewUrl()
    {
        return $this->getUrl('*/adminhtml_order_create/index');
    }
}
