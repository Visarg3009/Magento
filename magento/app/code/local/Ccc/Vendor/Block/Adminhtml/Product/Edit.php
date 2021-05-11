<?php

class Ccc_Vendor_Block_Adminhtml_Product_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'vendor';
        $this->_controller = 'adminhtml_product';
        $this->_updateButton('save', 'label', Mage::helper('vendor')->__('Save Product'));
        $this->_updateButton('delete', 'label', Mage::helper('vendor')->__('Delete Product'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_product') && Mage::registry('current_product')->getId()) {
            return Mage::helper('vendor')->__("Edit Product '%s'", $this->htmlEscape(Mage::registry('current_product')->getFirstname()));
        } else {
            return Mage::helper('vendor')->__('Add Product');
        }
    }
}
