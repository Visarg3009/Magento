<?php

class Ccc_Seller_Block_Adminhtml_Seller_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('seller')->__('Seller Information'));
    }
    public function getSeller()
    {
        return Mage::registry('current_seller');
    }

    protected function _beforeToHtml()
    {

        $sellerAttributes = Mage::getResourceModel('seller/seller_attribute_collection');

        if (!$this->getSeller()->getId()) {
            foreach ($sellerAttributes as $attribute) {
                $default = $attribute->getDefaultValue();
                if ($default != '') {
                    $this->getSeller()->setData($attribute->getAttributeCode(), $default);
                }
            }
        }

        $attributeSetId = $this->getSeller()->getResource()->getEntityType()->getDefaultAttributeSetId();



        // $attributeSetId = 21;

        $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->setAttributeSetFilter($attributeSetId)
            ->setSortOrder()
            ->load();

        $defaultGroupId = 0;
        foreach ($groupCollection as $group) {
            if ($defaultGroupId == 0 or $group->getIsDefault()) {
                $defaultGroupId = $group->getId();
            }
        }


        foreach ($groupCollection as $group) {
            $attributes = array();
            foreach ($sellerAttributes as $attribute) {
                if ($this->getSeller()->checkInGroup($attribute->getId(), $attributeSetId, $group->getId())) {
                    $attributes[] = $attribute;
                }
            }

            if (!$attributes) {
                continue;
            }

            $active = $defaultGroupId == $group->getId();
            $block = $this->getLayout()->createBlock('seller/adminhtml_seller_edit_tab_attributes')
                ->setGroup($group)
                ->setAttributes($attributes)
                ->setAddHiddenFields($active)
                ->toHtml();
            $this->addTab('group_' . $group->getId(), array(
                'label'     => Mage::helper('seller')->__($group->getAttributeGroupName()),
                'content'   => $block,
                'active'    => $active
            ));
        }
        return parent::_beforeToHtml();
    }
}
