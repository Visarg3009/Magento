<?php

class Ccc_Seller_Block_Adminhtml_Seller_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sellerGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        //$this->setUseAjax(true);
        $this->setVarNameFilter('seller_filter');
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();

        $collection = Mage::getModel('seller/seller')->getCollection()
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addAttributeToSelect('email');

        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        $collection->joinAttribute(
            'firstname',
            'seller/firstname',
            'entity_id',
            null,
            'inner',
            $adminStore
        );

        $collection->joinAttribute(
            'lastname',
            'seller/lastname',
            'entity_id',
            null,
            'inner',
            $adminStore
        );
        $collection->joinAttribute(
            'email',
            'seller/email',
            'entity_id',
            null,
            'inner',
            $adminStore
        );

        $collection->joinAttribute(
            'id',
            'seller/entity_id',
            'entity_id',
            null,
            'inner',
            $adminStore
        );
        $this->setCollection($collection);;
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            array(
                'header' => Mage::helper('seller')->__('id'),
                'width'  => '50px',
                'index'  => 'id',
            )
        );
        $this->addColumn(
            'firstname',
            array(
                'header' => Mage::helper('seller')->__('First Name'),
                'width'  => '50px',
                'index'  => 'firstname',
            )
        );

        $this->addColumn(
            'lastname',
            array(
                'header' => Mage::helper('seller')->__('Last Name'),
                'width'  => '50px',
                'index'  => 'lastname',
            )
        );

        $this->addColumn(
            'email',
            array(
                'header' => Mage::helper('seller')->__('Email'),
                'width'  => '50px',
                'index'  => 'email',
            )
        );

        $this->addColumn(
            'action',
            array(
                'header'   => Mage::helper('seller')->__('Action'),
                'width'    => '50px',
                'type'     => 'action',
                'getter'   => 'getId',
                'actions'  => array(
                    array(
                        'caption' => Mage::helper('seller')->__('Delete'),
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

    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            array(
                'store' => $this->getRequest()->getParam('store'),
                'id'    => $row->getId()
            )
        );
    }
}
