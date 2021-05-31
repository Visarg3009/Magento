<?php
class Ccc_Order_Block_Adminhtml_Order_View_Form_AccountInfo extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'order';
        $this->_controller = 'adminhtml_order_view_form_accountInfo';
        $this->_headerText = Mage::helper('order')->__('Account Information');
        parent::__construct();
        $this->_removeButton('save');
        $this->_removeButton('back');
        $this->_removeButton('reset');
    }
}
