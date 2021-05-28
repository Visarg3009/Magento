<?php
class Ccc_Order_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _getCollectionClass()
    {
        return 'order/order_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('order_id', array(
            'header' => Mage::helper('order')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'order_id',
        ));

        $this->addColumn('customer_name', array(
            'header' => Mage::helper('order')->__('Customer Name'),
            'index' => 'customer_name',
        ));

        $this->addColumn('customer_email', array(
            'header' => Mage::helper('order')->__('Customer Email'),
            'index' => 'customer_email',
        ));

        $this->addColumn('payment_method_code', array(
            'header' => Mage::helper('order')->__('Payment Method'),
            'index' => 'payment_method_code',
        ));
        $this->addColumn('shipping_method_code', array(
            'header' => Mage::helper('order')->__('Shipping Method'),
            'index' => 'shipping_method_code',
        ));

        $this->addColumn('total', array(
            'header' => Mage::helper('order')->__('Total'),
            'index' => 'total',
        ));

        $this->addColumn('shipping_amount', array(
            'header' => Mage::helper('order')->__('Shipping Charge'),
            'index' => 'shipping_amount',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('order')->__('Grand Total'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        // if (Mage::getSingleton('admin/session')->isAllowed('order/order/actions/view')) {
        //     $this->addColumn(
        //         'action',
        //         array(
        //             'header'    => Mage::helper('order')->__('Action'),
        //             'width'     => '50px',
        //             'type'      => 'action',
        //             'getter'     => 'getId',
        //             'actions'   => array(
        //                 array(
        //                     'caption' => Mage::helper('order')->__('View'),
        //                     'url'     => array('base' => '*/order_order/view'),
        //                     'field'   => 'order_id',
        //                     'data-column' => 'action',
        //                 )
        //             ),
        //             'filter'    => false,
        //             'sortable'  => false,
        //             'index'     => 'stores',
        //             'is_system' => true,
        //         )
        //     );
        // }

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('order/order/actions/view')) {
            return $this->getUrl('*/order_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
