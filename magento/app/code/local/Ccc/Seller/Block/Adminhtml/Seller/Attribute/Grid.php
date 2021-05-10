<?php
class Ccc_Seller_Block_Adminhtml_Seller_Attribute_Grid extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    /**
     * Prepare product attributes grid collection object
     *
     * @return Ccc_Seller_Block_Adminhtml_Seller_Attribute_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('seller/seller_attribute_collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare product attributes grid columns
     *
     * @return Ccc_Seller_Block_Adminhtml_Seller_Attribute_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter('is_visible', array(
            'header' => Mage::helper('catalog')->__('Visible'),
            'sortable' => true,
            'index' => 'is_visible_on_front',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
        ), 'frontend_label');

        $this->addColumnAfter('is_global', array(
            'header' => Mage::helper('catalog')->__('Scope'),
            'sortable' => true,
            'index' => 'is_global',
            'type' => 'options',
            'options' => array(
                Ccc_Seller_Model_Resource_Eav_Attribute::SCOPE_STORE => Mage::helper('catalog')->__('Store View'),
                Ccc_Seller_Model_Resource_Eav_Attribute::SCOPE_WEBSITE => Mage::helper('catalog')->__('Website'),
                Ccc_Seller_Model_Resource_Eav_Attribute::SCOPE_GLOBAL => Mage::helper('catalog')->__('Global'),
            ),
            'align' => 'center',
        ), 'is_visible');

        $this->addColumn('is_searchable', array(
            'header' => Mage::helper('catalog')->__('Searchable'),
            'sortable' => true,
            'index' => 'is_searchable',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
        ), 'is_user_defined');

        $this->addColumnAfter('is_filterable', array(
            'header' => Mage::helper('catalog')->__('Use in Layered Navigation'),
            'sortable' => true,
            'index' => 'is_filterable',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Filterable (with results)'),
                '2' => Mage::helper('catalog')->__('Filterable (no results)'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
        ), 'is_searchable');

        $this->addColumnAfter('is_comparable', array(
            'header' => Mage::helper('catalog')->__('Comparable'),
            'sortable' => true,
            'index' => 'is_comparable',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ),
            'align' => 'center',
        ), 'is_filterable');

        return $this;
    }
}
