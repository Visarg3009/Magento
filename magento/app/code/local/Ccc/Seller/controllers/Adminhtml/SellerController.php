<?php
class Ccc_Seller_Adminhtml_SellerController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('seller/seller');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('seller');
        $this->_title('Seller Grid');
        $this->_addContent($this->getLayout()->createBlock('seller/adminhtml_seller'));
        $this->renderLayout();
    }

    protected function _initSeller()
    {
        $this->_title($this->__('Seller'))
            ->_title($this->__('Manage sellers'));

        $sellerId = (int) $this->getRequest()->getParam('id');
        $seller   = Mage::getModel('seller/seller')
            ->setStoreId($this->getRequest()->getParam('store', 0))
            ->load($sellerId);

        Mage::register('current_seller', $seller);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $seller;
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $sellerId = (int) $this->getRequest()->getParam('id');
        $seller = $this->_initSeller();

        if ($sellerId && !$seller->getId()) {
            $this->_getSession()->addError(Mage::helper('seller')->__('This seller no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        // if ($seller->getId()) {
        //     Mage::register('Seller_data', $seller);
        // }

        $this->_title($seller->getName());
        $this->loadLayout();
        $this->_setActiveMenu('seller');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function saveAction()
    {
        try {
            $sellerData = $this->getRequest()->getPost('account');
            $seller = Mage::getSingleton('seller/seller');
            //$seller = Mage::getModel('seller/seller');

            if ($sellerId = $this->getRequest()->getParam('id')) {
                if (!$seller->load($sellerId)) {
                    throw new Exception("No Row Found");
                }
                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
            }

            $seller->addData($sellerData);
            $seller->save();

            Mage::getSingleton('core/session')->addSuccess("Seller data added.");
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction()
    {
        try {

            $sellerModel = Mage::getModel('seller/seller');

            if (!($sellerId = (int) $this->getRequest()->getParam('id')))
                throw new Exception('Id not found');

            if (!$sellerModel->load($sellerId)) {
                throw new Exception('seller does not exist');
            }

            if (!$sellerModel->delete()) {
                throw new Exception('Error in delete record', 1);
            }

            Mage::getSingleton('core/session')->addSuccess($this->__('The seller has been deleted.'));
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }

        $this->_redirect('*/*/');
    }
}
