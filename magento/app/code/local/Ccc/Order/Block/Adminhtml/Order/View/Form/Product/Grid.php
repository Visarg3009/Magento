<?php
class Ccc_Order_Block_Adminhtml_Order_View_Form_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('order_create_form_product_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _getCollectionClass()
    {
        return 'catalog/product';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price');

        $collection->joinField(
            'qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );
        $collection->joinAttribute(
            'name',
            'catalog_product/name',
            'entity_id',
            null,
            'inner',
        );
        $collection->joinAttribute(
            'price',
            'catalog_product/price',
            'entity_id',
            null,
            'left',
        );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('entity_id', array(
            'header' => Mage::helper('order')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'entity_id',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('order')->__('Product Name'),
            'index' => 'name',
            'width' => '100px',
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('order')->__('SKU'),
            'index' => 'sku',
        ));

        $this->addColumn('price', array(
            'header' => Mage::helper('order')->__('Price'),
            'index' => 'price',
        ));
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('add', array(
            'label' => Mage::helper('order')->__('Add Items'),
            'url'  => $this->getUrl('*/*/massAddition', array('_current' => true)),
            'confirm' => Mage::helper('order')->__('Are you sure?')
        ));

        Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));
        return $this;
    }

    public function getRowUrl($row)
    {
        // if (Mage::getSingleton('admin/session')->isAllowed('order/order/actions/view')) {
        //     return $this->getUrl('*/order_order/view', array('order_id' => $row->getId()));
        // }
        // return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
