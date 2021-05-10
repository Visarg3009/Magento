<?php

class Ccc_Seller_Block_Adminhtml_Seller_Attribute_Set_Toolbar_Add extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('seller/attribute/set/toolbar/add.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('seller')->__('Save Seller Attribute Set'),
                    'onclick'   => 'if (addSet.submit()) disableElements(\'save\');',
                    'class' => 'save'
                ))
        );
        $this->setChild(
            'back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('seller')->__('Back'),
                    'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/') . '\')',
                    'class' => 'back'
                ))
        );

        $this->setChild(
            'setForm',
            $this->getLayout()->createBlock('seller/adminhtml_seller_attribute_set_main_formset')
        );
        return parent::_prepareLayout();
    }

    protected function _getHeader()
    {
        return Mage::helper('seller')->__('Add New Seller Attribute Set');
    }

    protected function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    protected function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    protected function getFormHtml()
    {
        return $this->getChildHtml('setForm');
    }

    protected function getFormId()
    {
        return $this->getChild('setForm')->getForm()->getId();
    }
}
