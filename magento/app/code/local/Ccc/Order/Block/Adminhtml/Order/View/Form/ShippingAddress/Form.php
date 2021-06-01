<?php
class Ccc_Order_Block_Adminhtml_Order_View_Form_ShippingAddress_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected $order = null;
    protected $shippingAddress = null;

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
            'name' => 'shipping[name]',
            'label' => 'Name',
            'required' => true
        ]);
        $fieldset->addField('address', 'text', [
            'name' => 'shipping[address]',
            'label' => 'Street Address',
            'required' => true
        ]);
        $fieldset->addField('city', 'text', [
            'name' => 'shipping[city]',
            'label' => 'City',
            'required' => true
        ]);
        $fieldset->addField('country', 'text', [
            'name' => 'shipping[country]',
            'label' => 'Country',
            'required' => true
        ]);

        $fieldset->addField('zipcode', 'text', [
            'name' => 'shipping[zipcode]',
            'label' => 'Zipcode',
            'required' => true
        ]);


        if (Mage::registry('shipping_data')) {
            $form->setValues(Mage::registry('shipping_data')->getData());
        }
        return parent::_prepareForm();
    }

    public function getCountryOptions()
    {
        return Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
    }

    public function setOrder(Ccc_Order_Model_Order $order)
    {
        $this->order = $order;
        return $this;
    }

    public function getOrder()
    {
        if (!$this->order) {
            Mage::throwException(Mage::helper('order')->__('Order Is not set.'));
        }
        return $this->order;
    }

    public function setShippingAddress($shippingAddress = null)
    {
        $address = $this->getOrder()->getShippingAddress();

        $this->shippingAddress = $address;
        return $this;
    }

    public function getShippingAddress()
    {
        if (!$this->shippingAddress) {
            $this->setShippingAddress();
        }
        return $this->shippingAddress;
    }
}
