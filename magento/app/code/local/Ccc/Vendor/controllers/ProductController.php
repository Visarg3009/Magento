<?php
class Ccc_Vendor_ProductController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $session = $this->_getSession();
        if (!$session->isLoggedIn()) {
            $this->_redirect('vendor/account/login');
            return;
        }
        $this->loadLayout();
        $this->_initLayoutMessages('vendor/session');
        $this->renderLayout();
    }


    protected function _initProduct()
    {
        $this->_title($this->__('Vendor'))
            ->_title($this->__('Manage products'));

        $productId = (int) $this->getRequest()->getParam('id');
        $product   = Mage::getModel('vendor/product')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if (!$productId) {
            if ($setId = (int) $this->getRequest()->getParam('set')) {
                $product->setAttributeSetId($setId);
            }

            if ($typeId = (int)$this->getRequest()->getParam('type')) {
                $product->setAttributeTypeId($typeId);
            }
        } else {
            $product->load($productId);
        }
        Mage::register('current_product', $product);
        return $product;
    }


    public function editAction()
    {
        $session = $this->_getSession();
        if (!$session->isLoggedIn()) {
            $this->_redirect('vendor/account/login');
            return;
        }
        $productId = (int)$this->getRequest()->getParam('id');
        $product = $this->_initProduct();

        if ($productId && !$product->getId()) {
            $this->_getSession()->addError(Mage::helper('product')->__('This product no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $productRequestModel = Mage::getResourceModel('vendor/product_request_collection')->addFieldToFilter('product_id', array('eq', $product->getId()))->load()->getLastItem();
        // if ($productRequestModel && $productRequestModel->getId()) {
        //     if ($productRequestModel->getApproveStatus() != "Approved") {
        //         $this->_getSession()->addError(Mage::helper('vendor')->__('The product is not approved yet.'));
        //         $this->_redirect('*/*/');
        //         return;
        //     }
        // }

        $this->loadLayout();
        $this->_initLayoutMessages('vendor/session');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    protected function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }

    public function saveAction()
    {
        $session = $this->_getSession();
        if (!$session->isLoggedIn()) {
            $this->_redirect('vendor/account/login');
            return;
        }

        if ($this->getRequest()->isPost()) {
            $product = $this->_getProduct();

            $data = $this->getRequest()->getPost('product');

            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $data[$key] = implode(',', $value);
                }
            }

            if ($data) {
                try {
                    $productId = $this->getRequest()->getParam('id');
                    if ($productId) {
                        $product = $product->load($productId);
                        if (!$product) {
                            throw new Mage_Core_Exception("No product found.");
                        }
                    }

                    if ($product->getSku() != $data['sku']) {
                        if ($product->loadBySku($data['sku'])) {
                            throw new Mage_Core_Exception("This SKU is already in product.");
                        }
                    }

                    $product->addData($data);
                    $vendorId = (int)$this->_getSession()->getVendor()->getId();
                    $attributeDefaultSetId = Mage::getModel('vendor/product')->getResource()->getEntityType()->getDefaultAttributeSetId();
                    $product->setAttributeSetId($attributeDefaultSetId);
                    $product->setParentId($vendorId);
                    $product->setVendorId($vendorId);

                    $product = $product->save();
                    if ($product) {
                        if (!$productId) {
                            $productRequestModel = Mage::getModel('vendor/product_request');
                            $productRequestModel->setVendorId($product->getVendorId());
                            $productRequestModel->setProductId($product->getId());
                            $productRequestModel->setRequestType('New Added');
                            $productRequestModel->setApproveStatus('Pending');
                            $productRequestModel->setCreatedAt(time());
                            $productRequestModel->save();
                        } else {
                            $productRequestModel = Mage::getResourceModel('vendor/product_request_collection')->addFieldToFilter('product_id', array('eq', $product->getId()))->load()->getLastItem();
                            $productRequestModel->setRequestType('Edited');
                            $productRequestModel->setApproveStatus('Pending');
                            $productRequestModel->setCreatedAt($product->getCreatedAt());
                            $productRequestModel->save();
                        }
                    }
                    $session->addSuccess($this->__('Product save successfully.'));
                    $this->_redirect('*/*/');
                } catch (Exception $e) {
                    $session->addError($e->getMessage());
                    $this->_redirect('*/*/edit');
                }
            }
        }
        $this->_redirect('*/*/');
    }

    protected function _getProduct()
    {
        $product = $this->_getFromRegistry('current_product');
        if (!$product) {

            $product = $this->_getModel('vendor/product')->setId(null);
        }
        return $product;
    }

    protected function _getFromRegistry($path)
    {
        return Mage::registry($path);
    }

    public function _getModel($path, $arguments = array())
    {
        return Mage::getModel($path, $arguments);
    }

    public function deleteAction()
    {
        try {
            $productModel = Mage::getModel('vendor/product');

            if (!($productId = (int) $this->getRequest()->getParam('id')))
                throw new Exception('Id not found');

            if (!$productModel->load($productId)) {
                throw new Exception('product does not exist');
            }
            $productRequestModel = Mage::getResourceModel('vendor/product_request_collection')->addFieldToFilter('product_id', array('eq', $productModel->getId()))->load()->getLastItem();

            if ($productRequestModel->getApproveStatus() != "Approved") {
                $this->_getSession()->addError(Mage::helper('vendor')->__('The product is not approved yet.'));
                $this->_redirect('*/*/');
                return;
            }

            $productRequestModel->setRequestType('Deleted');
            $productRequestModel->setApproveStatus('Pending');
            $productRequestModel->setCreatedAt($productModel->getCreatedAt());
            $productRequestModel->save();
            Mage::getSingleton('core/session')->addSuccess($this->__('Delete request sent successfully.'));
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }
}
