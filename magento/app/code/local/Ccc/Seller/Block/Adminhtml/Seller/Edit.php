<?php

class Ccc_Seller_Block_Adminhtml_Seller_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'seller';
        $this->_controller = 'adminhtml_seller';
        parent::__construct();
    }
}
