<?php

class Ccc_Vendor_Block_Adminhtml_Product_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{


    public function __construct()
    {
        parent::__construct();
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('vendor')->__('Product Information'));
    }

    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    protected function _beforeToHtml()
    {
        $product = $this->getProduct();

        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }

        if ($setId) {
            $productAttributes = Mage::getResourceModel('vendor/product_attribute_collection');

            if (!$this->getProduct()->getId()) {
                foreach ($productAttributes as $attribute) {
                    $default = $attribute->getDefaultValue();
                    if ($default != '') {
                        $this->getProduct()->setData($attribute->getAttributeCode(), $default);
                    }
                }
            }

            // $attributeSetId = $this->getVendor()->getResource()->getEntityType()->getDefaultAttributeSetId();



            // $attributeSetId = 21;

            $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
                ->setAttributeSetFilter($setId)
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
                foreach ($productAttributes as $attribute) {
                    if ($this->getVendor()->checkInGroup($attribute->getId(), $setId, $group->getId())) {
                        $attributes[] = $attribute;
                    }
                }

                if (!$attributes) {
                    continue;
                }

                $active = $defaultGroupId == $group->getId();
                $block = $this->getLayout()->createBlock('vendor/adminhtml_vendor_edit_tab_attributes')
                    ->setGroup($group)
                    ->setAttributes($attributes)
                    ->setAddHiddenFields($active)
                    ->toHtml();
                $this->addTab('group_' . $group->getId(), array(
                    'label'     => Mage::helper('vendor')->__($group->getAttributeGroupName()),
                    'content'   => $block,
                    'active'    => $active
                ));
            }
        } else {
            $this->addTab('set', array(
                'label'     => Mage::helper('catalog')->__('Settings'),
                'content'   => $this->_translateHtml($this->getLayout()
                    ->createBlock('vendor/adminhtml_vendor_edit_tab_settings')->toHtml()),
                'active'    => true
            ));
        }
        return parent::_beforeToHtml();
    }

    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }
}
