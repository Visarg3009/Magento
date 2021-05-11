<?php


class Ccc_Vendor_Block_Product_Edit_Tabs extends Mage_Core_Block_Template
{
    protected $_attributeTabBlock = 'vendor/product_edit_tab_attributes';

    public function getGroupAttributes($group)
    {
        $attributeGroupId = $group->getAttributeGroupId();
        $attributeDefaultSetId = Mage::getModel('vendor/product')->getResource()->getEntityType()->getDefaultAttributeSetId();
        $model =  Mage::getModel('eav/entity_attribute')->setAttributeSetId($attributeDefaultSetId);
        $collection = $model->getResourceCollection()->setAttributeSetFilter($attributeDefaultSetId)->setAttributeGroupFilter($attributeGroupId)->load()->getItems();
        return $collection;
    }

    public function getGroups()
    {
        $vendorId = $this->_getSession()->getVendor()->getId();
        $attributeDefaultSetId = Mage::getModel('vendor/product')->getResource()->getEntityType()->getDefaultAttributeSetId();

        $vendorAttributeGroups =  Mage::getResourceModel('vendor/product_attribute_group_collection');
        $vendorAttributeGroups->addFieldToFilter('entity_id', array('eq' => $vendorId))->getSelect();
        $vendorAttributeGroups = $vendorAttributeGroups->load();

        $vendorProductAttributeGroups =  Mage::getResourceModel('eav/entity_attribute_group_collection');
        $vendorProductAttributeGroups->setAttributeSetFilter($attributeDefaultSetId)->addFieldToFilter('attribute_group_name', array('nin' => array('Design', 'Recurring Profile')))->setSortOrder()->getSelect()->where("attribute_group_name REGEXP '^[A-z]' ");

        $vendorAttributeGroups = array_merge($vendorProductAttributeGroups->getItems(), $vendorAttributeGroups->getItems());

        return $vendorAttributeGroups;
    }

    protected function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }

    /**
     * Retrive product object from object if not from registry
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!($this->getData('product') instanceof Ccc_Vendor_Model_Product)) {
            $this->setData('product', Mage::registry('product'));
        }
        return $this->getData('product');
    }
}
