<?php
class Ccc_Order_Block_Adminhtml_Order_View_Form_OrderItems_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $order = null;

    public function __construct()
    {
        parent::__construct();
        $this->setId('order_create_form_orderItems_grid');
        //$this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    public function setOrder(Ccc_Order_Model_Order $order)
    {
        $this->order = $order;
        return $this;
    }

    public function getOrder()
    {
        if (!$this->order) {
            Mage::throwException(Mage::helper('order')->__('Order Is not set.'));
        }
        return $this->order;
    }


    protected function _prepareCollection()
    {
        // $collection = Mage::getModel('order/cart_item')->getCollection()
        //     ->addFieldToFilter('cart_id', array('eq', $this->getCartId()));
        // $collection->getSelect()->join();
        $collection = $this->getOrder()->getItems();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('item_id', array(
            'header' => Mage::helper('order')->__('Item ID'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'item_id',
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
        $this->addColumn('base_price', array(
            'header' => Mage::helper('order')->__('BasePrice'),
            'index' => 'base_price',
        ));

        $this->addColumn('price', array(
            'header' => Mage::helper('order')->__('Price'),
            'index' => 'price',
        ));
        $this->addColumn('quantity', array(
            'header' => Mage::helper('order')->__('Quantity'),
            'index' => 'quantity',
        ));
        return parent::_prepareColumns();
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
        //return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
