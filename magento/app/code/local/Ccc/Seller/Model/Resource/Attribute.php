<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog attribute resource model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Ccc_Seller_Model_Resource_Attribute extends Mage_Eav_Model_Resource_Entity_Attribute
{
    /**
     * Perform actions before object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Catalog_Model_Resource_Attribute
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $applyTo = $object->getApplyTo();
        if (is_array($applyTo)) {
            $object->setApplyTo(implode(',', $applyTo));
        }
        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @param  Mage_Core_Model_Abstract $object
     * @return Mage_Catalog_Model_Resource_Attribute
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $this->_clearUselessAttributeValues($object);
        return parent::_afterSave($object);
    }

    /**
     * Clear useless attribute values
     *
     * @param  Mage_Core_Model_Abstract $object
     * @return Mage_Catalog_Model_Resource_Attribute
     */
    protected function _clearUselessAttributeValues(Mage_Core_Model_Abstract $object)
    {
        $origData = $object->getOrigData();

        if (
            $object->isScopeGlobal()
            && isset($origData['is_global'])
            && Ccc_Seller_Model_Resource_Eav_Attribute::SCOPE_GLOBAL != $origData['is_global']
        ) {
            $attributeStoreIds = array_keys(Mage::app()->getStores());
            if (!empty($attributeStoreIds)) {
                $delCondition = array(
                    'entity_type_id=?' => $object->getEntityTypeId(),
                    'attribute_id = ?' => $object->getId(),
                    'store_id IN(?)'   => $attributeStoreIds
                );
                $this->_getWriteAdapter()->delete($object->getBackendTable(), $delCondition);
            }
        }

        return $this;
    }

    /**
     * Delete entity
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Catalog_Model_Resource_Attribute
     */
    public function deleteEntity(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getEntityAttributeId()) {
            return $this;
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('eav/entity_attribute'))
            ->where('entity_attribute_id = ?', (int)$object->getEntityAttributeId());
        $result = $this->_getReadAdapter()->fetchRow($select);

        if ($result) {
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Ccc_Seller_Model_Resource_Seller::ENTITY, $result['attribute_id']);

            $backendTable = $attribute->getBackend()->getTable();
            if ($backendTable) {
                $select = $this->_getWriteAdapter()->select()
                    ->from($attribute->getEntity()->getEntityTable(), 'entity_id')
                    ->where('attribute_set_id = ?', $result['attribute_set_id']);

                $clearCondition = array(
                    'entity_type_id =?' => $attribute->getEntityTypeId(),
                    'attribute_id =?'   => $attribute->getId(),
                    'entity_id IN (?)'  => $select
                );
                $this->_getWriteAdapter()->delete($backendTable, $clearCondition);
            }
        }

        $condition = array('entity_attribute_id = ?' => $object->getEntityAttributeId());
        $this->_getWriteAdapter()->delete($this->getTable('entity_attribute'), $condition);

        return $this;
    }
}
