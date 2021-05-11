<?php

class Ccc_Vendor_Block_Product_Grid extends Mage_Core_Block_Template
{
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('vendor/product')->getCollection()->addFieldToFilter('parent_id', array('eq', $this->getVendor()->getId()))
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('sku');
        $collection->joinAttribute(
            'id',
            'vendor_product/entity_id',
            'entity_id',
            null,
            'inner',
        );
        $collection->getSelect()->join(
            array('vendor_product_request' => 'vendor_product_request'),
            'vendor_product_request.product_id = e.entity_id',
            array('vendor_product_request.request_type', 'vendor_product_request.approve_status')
        );
        return $collection;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getAddUrl()
    {
        return $this->getUrl('*/*/edit');
    }

    protected function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }

    public function getVendor()
    {
        return $this->_getSession()->getVendor();
    }

    public function getEditUrl($product)
    {
        return $this->getUrl('*/*/edit', ['id' => $product['entity_id']]);
    }


    public function getDeleteUrl($product)
    {
        return $this->getUrl('*/*/delete', ['id' => $product['entity_id']]);
    }
}
