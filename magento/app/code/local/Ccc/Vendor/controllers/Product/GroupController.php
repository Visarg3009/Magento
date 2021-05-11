<?php

class Ccc_Vendor_Product_GroupController extends Mage_Core_Controller_Front_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }

    public function indexAction()
    {
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('vendor/account/login');
            return;
        }
        $this->loadLayout();
        $this->_initLayoutMessages('vendor/session');
        $this->renderLayout();
    }

    public function newAction()
    {
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('vendor/account/login');
            return;
        }
        $this->loadLayout();
        $this->_initLayoutMessages('vendor/session');
        $this->renderLayout();
    }

    public function saveAction()
    {
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('vendor/account/login');
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if ($data) {
                $vendorId = $this->_getSession()->getId();
                $attributeDefaultSetId = Mage::getModel('vendor/product')->getResource()->getEntityType()->getDefaultAttributeSetId();
                $model = Mage::getModel('eav/entity_attribute_group');

                $nameArray = explode(" ", $data['attribute_group_name']);
                $array = [];
                foreach ($nameArray as $name) {
                    $array[] = strtolower($name);
                }
                $groupName = $vendorId . '_' . implode("_", $array);
                $model->setAttributeGroupName($groupName)
                    ->setAttributeSetId($attributeDefaultSetId);
                $id = $this->getRequest()->getParam('group_id');

                if (!$id) {
                    if ($model->itemExists()) {
                        Mage::getSingleton('vendor/session')->addError(Mage::helper('vendor')->__('A group with the same name already exists.'));
                    }
                }
                $groupModel = Mage::getModel('vendor/product_attribute_group');
                if ($id) {
                    if ($groupModel->load($id)) {
                        $model->setId($groupModel->getEntityId());
                    }
                }

                try {
                    $model->save();
                    // echo '<pre>';
                    // print_r($groupModel);
                    $groupModel->setAttributeGroupName($data['attribute_group_name']);
                    if (!$id) {
                        $groupModel->setEntityId($vendorId);
                        $groupModel->setAttributeGroupId($model->getId());
                    }
                    $groupModel->save();
                    Mage::getSingleton('vendor/session')->addSuccess(Mage::helper('vendor')->__('Group has been saved.'));
                } catch (Exception $e) {
                    Mage::getSingleton('vendor/session')->addError(Mage::helper('vendor')->__('An error occurred while saving this group.'));
                    $this->_redirect('*/*/');
                }
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('attribute_group_id');
        $model = Mage::getModel('eav/entity_attribute_group');
        if (!$model->load($id)) {
            Mage::getSingleton('vendor/session')->addError(
                Mage::helper('vendor')->__('This group cannot be deleted.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $model->delete();
        Mage::getSingleton('vendor/session')->addSuccess(
            Mage::helper('vendor')->__('Group is deleted.')
        );
        $this->_redirect('*/*/');
    }

    public function getGroup()
    {
        $groupModel = Mage::getModel('vendor/product_attribute_group');
        if ($id = $this->getRequest()->getParam('group_id')) {
            $group = $groupModel->load($id);
            if ($group) {
                return $group;
            }
        }
        return $groupModel;
    }

    public function updateAction()
    {
        $id = $this->getRequest()->getParam('group_id');
        $attributeGroupId = $this->getGroup()->getAttributeGroupId();
        $data = $this->getRequest()->getPost();
        if ($this->getRequest()->isPost()) {
            try {
                if ($data['removeGroupAttributes']) {
                    $removeGroupAttributes = $data['removeGroupAttributes'];
                    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
                    foreach ($removeGroupAttributes as $removeGroupAttribute) {
                        $attributeDefaultSetId = Mage::getModel('vendor/product')->getResource()->getEntityType()->getDefaultAttributeSetId();
                        $query = "DELETE FROM `eav_entity_attribute` WHERE `attribute_id` = '$removeGroupAttribute' AND `attribute_set_id` = '$attributeDefaultSetId'";
                        $setup->getConnection()->query($query);
                    }
                }
                if ($data['assignAttributes']) {
                    $assignAttributes = $data['assignAttributes'];
                    $attributeDefaultSetId = Mage::getModel('vendor/product')->getResource()->getEntityType()->getDefaultAttributeSetId();
                    $entityTypeId = Mage::getModel('vendor/product')->getResource()->getEntityType()->getId();

                    foreach ($assignAttributes as $assignAttribute) {
                        $model =  Mage::getModel('eav/entity_attribute');
                        $model->setAttributeGroupId($attributeGroupId);
                        $model->setAttributeSetId($attributeDefaultSetId);
                        $model->setEntityTypeId($entityTypeId);
                        $model->setAttributeId($assignAttribute);
                        $model->save();
                    }
                }
                Mage::getSingleton('vendor/session')->addSuccess(Mage::helper('vendor')->__('Group has been saved.'));
            } catch (Exception $e) {
                Mage::getSingleton('vendor/session')->addError($e->getMessage());
            }
            $this->_redirect('*/*/new', ['group_id' => $id]);
        }
    }
}
