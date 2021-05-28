<?php
class Ccc_Order_Block_Adminhtml_Order_Create_Form_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'order';
        $this->_controller = 'adminhtml_order_create_form_product';
        $this->_headerText = Mage::helper('order')->__('Please Select Products To Add');
        $this->_addButtonLabel = Mage::helper('order')->__('Add Select Product(s) To Order');
        parent::__construct();
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/adminhtml_order_create/addProducts');
    }
}
