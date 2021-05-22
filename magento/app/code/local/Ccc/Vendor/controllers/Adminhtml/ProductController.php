<?php

class Ccc_Vendor_AdminHtml_ProductController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('vendor/vendor');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('Vendor/vendor');
        $this->_title('Product Grid');
        $this->_addContent($this->getLayout()->createBlock('vendor/adminhtml_product'));
        $this->renderLayout();
    }

    protected function _initProduct()
    {
        $this->_title($this->__('Product'))
            ->_title($this->__('Manage products'));

        $productId = (int) $this->getRequest()->getParam('id');
        $product   = Mage::getModel('vendor/product')
            ->setStoreId($this->getRequest()->getParam('store', 0))
            ->load($productId);
        if (!$productId) {
            if ($setId = (int) $this->getRequest()->getParam('set')) {
                Mage::getModel('adminhtml/session')->setId($setId);
            }
        }
        Mage::register('current_product', $product);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $product;
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $productId = (int)$this->getRequest()->getParam('id');
        $product = $this->_initProduct();

        if ($productId && !$product->getId()) {
            $this->_getSession()->addError(Mage::helper('vendor')->__('This product no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($product->getName());

        $this->loadLayout();

        $this->_setActiveMenu('Product/vendor');

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->renderLayout();
    }

    public function saveAction()
    {

        try {
            $productData = $this->getRequest()->getPost('account');
            $setId = Mage::getSingleton('adminhtml/session')->getId();
            $product = $this->_initProduct();
            $product->setAttributeSetId($setId);

            if ($productId = $this->getRequest()->getParam('id')) {

                if (!$product->load($productId)) {
                    throw new Exception("No Row Found");
                }
                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
            }
            $product->addData($productData);
            $product->save();

            Mage::getSingleton('core/session')->addSuccess("Product data added.");
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction()
    {
        try {

            $productModel = Mage::getModel('vendor/product');

            if (!($productId = (int) $this->getRequest()->getParam('id')))
                throw new Exception('Id not found');

            if (!$productModel->load($productId)) {
                throw new Exception('vendor does not exist');
            }

            if (!$productModel->delete()) {
                throw new Exception('Error in delete record', 1);
            }

            Mage::getSingleton('core/session')->addSuccess($this->__('The product has been deleted.'));
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }


    public function approveAction()
    {
        $productId = (int)$this->getRequest()->getParam('id');
        $product = $this->_initProduct();
        try {
            if ($productId && !$product->getId()) {
                $this->_getSession()->addError(Mage::helper('vendor')->__('This product no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            $productRequestModel = Mage::getResourceModel('vendor/product_request_collection')->addFieldToFilter('product_id', array('eq', $product->getId()))->load()->getLastItem();

            if ($productRequestModel->getRequestType() == 'Deleted') {
                $this->_forward('deleteRequest');
                return;
            }
            if ($productRequestModel->getRequestType() == 'Edited' && $productRequestModel->getApproveStatus() == 'Approved') {
                $this->_forward('editRequest');
                return;
            }
            $catalogProductModel = Mage::getModel('catalog/product');
            $entityType = $catalogProductModel->getResource()->getEntityType();
            $attributeDefaultSetId = $entityType->getDefaultAttributeSetId();
            $data = $product->getData();
            unset($data['entity_id']);
            $catalogProductModel->addData($data);
            $catalogProductModel->setStoreId($this->getRequest()->getParam('store', 0));
            $catalogProductModel->setEntityType($entityType);
            $catalogProductModel->setAttributeSetId($attributeDefaultSetId);
            if ($catalogProductModel->save()) {
                $productRequestModel->setProductId($product->getId());
                $productRequestModel->setCatalogProductId($catalogProductModel->getId());
                $productRequestModel->setApproveStatus('Approved');
                $productRequestModel->setCreatedAt($product->getCreatedAt());
                $productRequestModel->setApprovedAt(time());
                $productRequestModel->save();
            }
            Mage::getSingleton('core/session')->addSuccess($this->__('The product has been Approved.'));
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }

    public function rejectAction()
    {
        $productId = (int)$this->getRequest()->getParam('id');
        $product = $this->_initProduct();
        try {
            if ($productId && !$product->getId()) {
                $this->_getSession()->addError(Mage::helper('vendor')->__('This product no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            $productRequestModel = Mage::getResourceModel('vendor/product_request_collection')->addFieldToFilter('product_id', array('eq', $product->getId()))->load()->getLastItem();
            $productRequestModel->setApproveStatus('Rejected');
            $productRequestModel->setCreatedAt($product->getCreatedAt());
            $productRequestModel->setApprovedAt(time());
            $productRequestModel->save();
            Mage::getSingleton('core/session')->addSuccess($this->__('The product has been Rejected.'));
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }


    public function editRequestAction()
    {
        $productId = (int) $this->getRequest()->getParam('id');
        $product   = Mage::getModel('vendor/product')
            ->setStoreId($this->getRequest()->getParam('store', 0))
            ->load($productId);
        if (!$productId) {
            if ($setId = (int) $this->getRequest()->getParam('set')) {
                Mage::getModel('adminhtml/session')->setId($setId);
            }
        }
        try {
            if ($productId && !$product->getId()) {
                $this->_getSession()->addError(Mage::helper('vendor')->__('This product no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            $data = $product->getData();
            unset($data['entity_id']);
            unset($data['entity_type']);
            unset($data['attribute_set_id']);
            unset($data['store_id']);
            $productRequestModel = Mage::getResourceModel('vendor/product_request_collection')->addFieldToFilter('product_id', array('eq', $product->getId()))->load()->getLastItem();
            $catalogProductModel = Mage::getModel('catalog/product');
            $catalogProductId = $productRequestModel->getCatalogProductId();
            if ($catalogProductModel->load($catalogProductId)) {
                $catalogProductModel->addData($data);
                $catalogProductModel->save();
                $productRequestModel->setEntityId(null);
                $productRequestModel->setRequestType('Edited');
                $productRequestModel->setApproveStatus('Approved');
                $productRequestModel->setCreatedAt($product->getUpdatedAt());
                $productRequestModel->setApprovedAt(time());
                $productRequestModel->save();
            }
            Mage::getSingleton('core/session')->addSuccess($this->__('The product has been Approved.'));
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/index');
    }

    public function deleteRequestAction()
    {
        try {
            $productId = (int) $this->getRequest()->getParam('id');
            $product   = Mage::getModel('vendor/product')->load($productId);

            if ($productId && !$product->getId()) {
                $this->_getSession()->addError(Mage::helper('vendor')->__('This product no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            if (!$product->delete()) {
                throw new Exception('Error in delete record', 1);
            }

            Mage::getSingleton('core/session')->addSuccess($this->__('The product has been deleted.'));
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }
}
