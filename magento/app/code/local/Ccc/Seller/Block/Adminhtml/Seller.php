<?php
class Ccc_Seller_Block_Adminhtml_Seller extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'seller';
        $this->_controller = 'adminhtml_seller';
        $this->_headerText = $this->__('Seller Grid');
        parent::__construct();
    }
}
