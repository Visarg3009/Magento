<?php
class Ccc_Vendor_Model_Product extends Mage_Core_Model_Abstract
{
    const ENTITY = 'vendor_product';
    protected $_attributes;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('vendor/product');
    }

    public function isRecurring()
    {
        return $this->getIsRecurring() == '1';
    }

    public function isLockedAttribute($attributeCode)
    {
        return isset($this->_lockedAttributes[$attributeCode]);
    }

    public function formatUrlKey($str)
    {
        return $this->getUrlModel()->formatUrlKey($str);
    }

    public function getUrlModel()
    {
        if ($this->_urlModel === null) {
            $this->_urlModel = Mage::getSingleton('catalog/factory')->getProductUrlInstance();
        }
        return $this->_urlModel;
    }

    public function getAttributes($groupId = null, $skipSuper = false)
    {
        $productAttributes = $this->getTypeInstance(true)->getEditableAttributes($this);
        if ($groupId) {
            $attributes = array();
            foreach ($productAttributes as $attribute) {
                if ($attribute->isInGroup($this->getAttributeSetId(), $groupId)) {
                    $attributes[] = $attribute;
                }
            }
        } else {
            $attributes = $productAttributes;
        }

        return $attributes;
    }

    public function checkInGroup($attributeId, $setId, $groupId)
    {

        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');
        $readConnection = $resource->getConnection('core_read');

        $query = '
            SELECT * FROM ' .
            $resource->getTableName('eav/entity_attribute')
            . ' WHERE `attribute_id` =' . $attributeId
            . ' AND `attribute_group_id` =' . $groupId
            . ' AND `attribute_set_id` =' . $setId;

        $results = $readConnection->fetchRow($query);

        if ($results) {
            return true;
        }
        return false;
    }


    public function loadBySku($productSku)
    {
        return $this->_getResource()->loadBySku($productSku);
    }
}
