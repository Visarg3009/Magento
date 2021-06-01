<?php
class Ccc_Order_Block_Adminhtml_Order_View_Form_PaymentMethod_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $order = null;

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

    public function __construct()
    {
        parent::__construct();
        $this->setId('order_create_form_paymentMethod_grid');
        //$this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    public function getPayemntMethodTitle()
    {
        $methods = Mage::getModel('payment/config');
        $activemethod = $methods->getActiveMethods();
        unset($activemethod['paypal_billing_agreement']);
        unset($activemethod['checkmo']);
        unset($activemethod['free']);
        return $activemethod;
    }
}
