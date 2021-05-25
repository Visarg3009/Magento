<?php


class Ccc_Vendor_Block_Adminhtml_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ProductGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('Vendor_filter');
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        // $productRequestModelCollection = Mage::getResourceModel('vendor/product_request_collection')->load()->getLastItem();
        $collection = Mage::getModel('vendor/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('product_status')
            ->addAttributeToSelect('vendor_id')
            ->addAttributeToSelect('price');

        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;

        $collection->joinAttribute(
            'id',
            'vendor_product/entity_id',
            'entity_id',
            null,
            'inner',
            $adminStore
        );

        $collection->getSelect()->join(
            array('vendor_product_request' => 'vendor_product_request'),
            'vendor_product_request.product_id = e.entity_id',
            array('vendor_product_request.request_type', 'vendor_product_request.approve_status')
        );


        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                'header' => Mage::helper('vendor')->__('id'),
                'width'  => '50px',
                'index'  => 'id',
            )
        );
        $this->addColumn(
            'name',
            array(
                'header' => Mage::helper('vendor')->__('Name'),
                'width'  => '50px',
                'index'  => 'name',
            )
        );

        $this->addColumn(
            'price',
            array(
                'header' => Mage::helper('vendor')->__('Price'),
                'width'  => '50px',
                'index'  => 'price',
            )
        );

        $this->addColumn(
            'vendor_id',
            array(
                'header' => Mage::helper('vendor')->__('Vendor Id'),
                'width'  => '50px',
                'index'  => 'vendor_id',
            )
        );

        $this->addColumn(
            'request_type',
            array(
                'header' => Mage::helper('vendor')->__('Request Type'),
                'width'  => '50px',
                'index'  => 'request_type',
            )
        );

        $this->addColumn(
            'approve_status',
            array(
                'header' => Mage::helper('vendor')->__('Approve Status'),
                'width'  => '50px',
                'index'  => 'approve_status',
            )
        );

        $this->addColumn(
            'action_1',
            array(
                'header'   => Mage::helper('vendor')->__('Action'),
                'width'    => '50px',
                'type'     => 'action',
                'getter'   => 'getId',
                'actions'  => array(
                    array(
                        'caption' => Mage::helper('vendor')->__('Approve'),
                        'url'     => array(
                            'base' => '*/*/approve',
                        ),
                        'field'   => 'id',
                    ),
                    array(
                        'caption' => Mage::helper('vendor')->__('Reject'),
                        'url'     => array(
                            'base' => '*/*/reject',
                        ),
                        'field'   => 'id',
                    ),
                ),
                'filter'   => false,
                'sortable' => false,
            )
        );
        $this->addColumn(
            'action',
            array(
                'header'   => Mage::helper('vendor')->__('Action'),
                'width'    => '50px',
                'type'     => 'action',
                'getter'   => 'getId',
                'actions'  => array(
                    array(
                        'caption' => Mage::helper('vendor')->__('Delete'),
                        'url'     => array(
                            'base' => '*/*/delete',
                        ),
                        'field'   => 'id',
                    ),
                ),
                'filter'   => false,
                'sortable' => false,
            )
        );

        parent::_prepareColumns();
        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/index', array('_current' => true));
    }
}
