<?php

class Ccc_Vendor_Block_Product_Edit extends Mage_Core_Block_Template
{
    public $groups = [];
    public $attribute = null;

    public function getValue($attribute)
    {
        $attributeCode = $attribute->getAttributeCode();
        return $this->getProduct()->getData()[$attributeCode];
    }

    public function getProduct()
    {
        $product = Mage::getModel('vendor/product');
        if (Mage::registry('current_product')) {
            return Mage::registry('current_product');
        }
        return $product;
    }

    public function getGroups()
    {
        $vendorId = $this->_getSession()->getVendor()->getId();
        $attributeDefaultSetId = Mage::getModel('vendor/product')->getResource()->getEntityType()->getDefaultAttributeSetId();

        $vendorAttributeGroups =  Mage::getResourceModel('vendor/product_attribute_group_collection');
        $vendorAttributeGroups->addFieldToFilter('entity_id', array('eq' => $vendorId))->getSelect();
        $vendorAttributeGroups = $vendorAttributeGroups->load();

        $vendorProductAttributeGroups =  Mage::getResourceModel('eav/entity_attribute_group_collection');
        $vendorProductAttributeGroups->setAttributeSetFilter($attributeDefaultSetId)->addFieldToFilter('attribute_group_name', array('nin' => array('Design', 'Recurring Profile')))->getSelect()->where("attribute_group_name REGEXP '^[A-z]' ");

        $vendorAttributeGroups = array_merge($vendorAttributeGroups->getItems(), $vendorProductAttributeGroups->getItems());

        $this->groups = $vendorAttributeGroups;

        return $this->groups;
    }

    protected function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }

    public function getSaveUrl()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            return $this->getUrl('*/*/save', ['id' => $id]);
        }
        return $this->getUrl('*/*/save');
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

    public function getGroupAttributes($group)
    {
        $attributeGroupId = $group->getAttributeGroupId();
        $attributeDefaultSetId = Mage::getModel('vendor/product')->getResource()->getEntityType()->getDefaultAttributeSetId();
        $model =  Mage::getModel('eav/entity_attribute')->setAttributeSetId($attributeDefaultSetId);
        $collection = $model->getResourceCollection()->setAttributeSetFilter($attributeDefaultSetId)->setAttributeGroupFilter($attributeGroupId)->addFieldToFilter('frontend_label', array('neq' => NULL))->addFieldToFilter('attribute_code', array('nin' => ['image_label', 'small_image_label', 'thumbnail_label', 'manufacturer', 'special_price', 'special_from_date', 'special_to_date', 'group_price', 'tier_price', 'minimal_price', 'cost', 'visibility']))->load()->getItems();
        return $collection;
    }


    public function getOptionValues($attribute, $type)
    {
        $attributeType = $type;
        $defaultValues = $attribute->getDefaultValue();
        if ($attributeType == 'select' || $attributeType == 'multiselect') {
            $defaultValues = explode(',', $defaultValues);
        } else {
            $defaultValues = array();
        }

        switch ($attributeType) {
            case 'select':
                $inputType = 'radio';
                break;
            case 'multiselect':
                $inputType = 'checkbox';
                break;
            default:
                $inputType = '';
                break;
        }

        $values = $this->getData('option_values');
        $values = array();
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute->getId())
            ->setPositionOrder('desc', true)
            ->load();

        foreach ($optionCollection as $option) {
            $value = array();
            if (in_array($option->getId(), $defaultValues)) {
                $value['checked'] = 'checked="checked"';
            } else {
                $value['checked'] = '';
            }
            $value['intype'] = $inputType;
            $value['id'] = $option->getId();
            $value['sort_order'] = $option->getSortOrder();
            $value['value'] = $option->getValue();
            $values[] = new Varien_Object($value);
        }
        $this->setData('option_values', $values);
        return $values;
    }
}
