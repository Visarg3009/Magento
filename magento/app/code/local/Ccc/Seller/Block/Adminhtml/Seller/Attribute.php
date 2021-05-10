<?php
class Ccc_Seller_Block_Adminhtml_Seller_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'seller';
        $this->_controller = 'adminhtml_seller_attribute';
        $this->_headerText = Mage::helper('seller')->__('Manage Attributes');
        $this->_addButtonLabel = Mage::helper('seller')->__('Add New Attribute');
        parent::__construct();
    }
}
