<?php

class Ccc_Seller_Block_Adminhtml_Seller_Attribute_Set_Main_Formset extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareForm()
    {
        $data = Mage::getModel('eav/entity_attribute_set')
            ->load($this->getRequest()->getParam('id'));

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('set_name', array('legend' => Mage::helper('seller')->__('Edit Set Name')));
        $fieldset->addField('attribute_set_name', 'text', array(
            'label' => Mage::helper('seller')->__('Name'),
            'note' => Mage::helper('seller')->__('For internal use.'),
            'name' => 'attribute_set_name',
            'required' => true,
            'class' => 'required-entry validate-no-html-tags',
            'value' => $data->getAttributeSetName()
        ));

        if (!$this->getRequest()->getParam('id', false)) {
            $fieldset->addField('gotoEdit', 'hidden', array(
                'name' => 'gotoEdit',
                'value' => '1'
            ));

            $sets = Mage::getModel('eav/entity_attribute_set')
                ->getResourceCollection()
                ->setEntityTypeFilter(Mage::registry('entityType'))
                ->load()
                ->toOptionArray();

            $fieldset->addField('skeleton_set', 'select', array(
                'label' => Mage::helper('seller')->__('Based On'),
                'name' => 'skeleton_set',
                'required' => true,
                'class' => 'required-entry',
                'values' => $sets,
            ));
        }

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('set_prop_form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setOnsubmit('return false;');
        $this->setForm($form);
    }
}
