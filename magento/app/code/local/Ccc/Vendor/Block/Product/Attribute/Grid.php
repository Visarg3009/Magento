<?php

class Ccc_Vendor_Block_Product_Attribute_Grid extends Mage_Core_Block_Template
{
    protected function getAttributeCollection()
    {
        $vendorId = Mage::getSingleton('vendor/session')->getVendor()->getId();
        $collection = Mage::getModel('vendor/resource_product_attribute_collection')->addFieldToFilter('attribute_code', array('like' => '%' . $vendorId . '%'))->getData();
        return $collection;
    }

    public function getAddUrl()
    {
        return $this->getUrl('*/*/new');
    }

    public function getEditUrl($attribute)
    {
        return $this->getUrl('*/*/edit', ['attribute_id' => $attribute['attribute_id']]);
    }
}
