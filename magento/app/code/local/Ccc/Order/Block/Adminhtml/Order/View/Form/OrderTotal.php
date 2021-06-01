<?php
class Ccc_Order_Block_Adminhtml_Order_View_Form_OrderTotal extends Mage_Adminhtml_Block_Template
{
    protected $order = null;

    public function __construct()
    {
        parent::__construct();
        $this->setId('adminhtml_order_view_form_orderTotal');
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
