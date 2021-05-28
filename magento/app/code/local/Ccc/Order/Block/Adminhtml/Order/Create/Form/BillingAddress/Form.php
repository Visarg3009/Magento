<?php
class Ccc_Order_Block_Adminhtml_Order_Create_Form_BillingAddress_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            [
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]),
                'method' => 'post',
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('display', [
            'class' => 'fieldset-wide'
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'billing[name]',
            'label' => 'Name',
            'required' => true
        ]);
        $fieldset->addField('address', 'text', [
            'name' => 'billing[address]',
            'label' => 'Street Address',
            'required' => true
        ]);
        $fieldset->addField('city', 'text', [
            'name' => 'billing[city]',
            'label' => 'City',
            'required' => true
        ]);
        $fieldset->addField('country', 'text', [
            'name' => 'billing[country]',
            'label' => 'Country',
            'required' => true
        ]);

        $fieldset->addField('zipcode', 'text', [
            'name' => 'billing[zipcode]',
            'label' => 'Zipcode',
            'required' => true
        ]);


        if (Mage::registry('billing_data')) {
            $form->setValues(Mage::registry('billing_data')->getData());
        }
        return parent::_prepareForm();
    }

    public function getBillingAddress()
    {
        return Mage::registry('billing_data');
    }

    public function getCountryOptions()
    {
        return Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
    }
}
