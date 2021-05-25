<?php
class Ccc_Vendor_Block_Product_Attribute_Group_Grid extends Mage_Core_Block_Template
{
    public function getAddGroupUrl()
    {
        return $this->getUrl('*/*/new');
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    protected function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }

    public function getGroups()
    {
        return Mage::getResourceModel('vendor/product_attribute_group_collection')->addFieldToFilter('entity_id', array('like' => '%' . $this->_getSession()->getVendor()->getId() . '%'))->getData();
    }

    public function getEditUrl($group)
    {
        return $this->getUrl('*/*/new', ['group_id' => $group['group_id']]);
    }

    public function getDeleteUrl($group)
    {
        return $this->getUrl('*/*/delete', ['attribute_group_id' => $group['attribute_group_id']]);
    }
}
