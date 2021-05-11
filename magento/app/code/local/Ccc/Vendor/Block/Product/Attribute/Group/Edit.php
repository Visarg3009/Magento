<?php

class Ccc_Vendor_Block_Product_Attribute_Group_Edit extends Mage_Core_Block_Template
{
    public function getSaveUrl()
    {
        if ($id = $this->getRequest()->getParam('group_id')) {
            return $this->getUrl('*/*/save', ['group_id' => $id]);
        }
        return $this->getUrl('*/*/save');
    }

    public function getUpdateUrl()
    {
        if ($id = $this->getRequest()->getParam('group_id')) {
            return $this->getUrl('*/*/update', ['group_id' => $id]);
        }
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

    public function getGroup()
    {
        $groupModel = Mage::getModel('vendor/product_attribute_group');
        if ($id = $this->getRequest()->getParam('group_id')) {
            $group = $groupModel->load($id);
            if ($group) {
                return $group;
            }
        }
        return $groupModel;
    }

    public function getGroupId()
    {
        $groupModel = Mage::getModel('vendor/product_attribute_group');
        if ($id = $this->getRequest()->getParam('group_id')) {
            $group = $groupModel->load($id);
            if ($group) {
                return $group->getId();
            }
        }
        return false;
    }

    protected function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }

    public function getAssignedAttributes()
    {
        $attributeDefaultSetId = Mage::getModel('vendor/product')->getResource()->getEntityType()->getDefaultAttributeSetId();
        $model =  Mage::getModel('eav/entity_attribute');
        $collection = $model->getResourceCollection()->setAttributeSetFilter($attributeDefaultSetId)->addFieldtoFilter('attribute_group_id', array('in' => $this->getGroups()))->getData();
        return $collection;
    }

    public function getAttributes()
    {
        $vendorId = $this->_getSession()->getVendor()->getId();
        $collection =  Mage::getResourceModel('vendor/product_attribute_collection')->addFieldToFilter('attribute_code', array('like' => '%' . $vendorId . '%'))->getData();
        return $collection;
    }

    public function getGroupAttributes()
    {
        $attributeGroupId = $this->getGroup()->getAttributeGroupId();
        $attributeDefaultSetId = Mage::getModel('vendor/product')->getResource()->getEntityType()->getDefaultAttributeSetId();
        $model =  Mage::getModel('eav/entity_attribute')->setAttributeSetId($attributeDefaultSetId);
        $collection = $model->getResourceCollection()->setAttributeSetFilter($attributeDefaultSetId)->setAttributeGroupFilter($attributeGroupId)->getData();
        return $collection;
    }

    public function getUnassignedAttributes()
    {
        $assignedAttributes = $this->getAssignedAttributes();
        $attributes = $this->getAttributes();

        if ($assignedAttributes) {
            foreach ($assignedAttributes as $assignedAttribute) {
                if ($attributes) {
                    foreach ($attributes as  $key => $attribute) {
                        if ($attribute['attribute_id'] == $assignedAttribute['attribute_id']) {
                            unset($attributes[$key]);
                        }
                    }
                }
            }
        }
        return $attributes;
    }

    public function getGroups()
    {
        $groups = [];
        $collection =  Mage::getResourceModel('vendor/product_attribute_group_collection')->addFieldToFilter('entity_id', array('like' => '%' . $this->_getSession()->getVendor()->getId() . '%'))->getData();
        foreach ($collection as $group) {
            $groups[] = $group['attribute_group_id'];
        }
        return $groups;
    }
}
