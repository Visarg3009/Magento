<?php
class Ccc_Order_Block_Adminhtml_Order_Create_Form_ShippingAddress_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected $cart = null;
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

    public function setCart(Ccc_Order_Model_Cart $cart)
    {
        $this->cart = $cart;
        return $this;
    }

    public function getCart()
    {
        if (!$this->cart) {
            Mage::throwException(Mage::helper('order')->__('Cart Is not set.'));
        }
        return $this->cart;
    }

    public function setShippingAddress($shippingAddress = null)
    {
        $address = $this->getCart()->getShippingAddress();
        if ($address->getData()) {
            $this->shippingAddress = $address;
            return $this;
        }

        $shippingAddress = $this->getCart()->getCustomer()->getDefaultShippingAddress();
        if ($shippingAddress) {
            $cartShippingAddress = $address;
            $cartShippingAddress->setCartId($this->getCart()->getId());
            $cartShippingAddress->setName($shippingAddress->getFirstname());
            $cartShippingAddress->setAddressType(Ccc_Order_Model_Cart_Address::ADDRESS_TYPE_SHIPPING);
            $cartShippingAddress->setAddress(implode(' ', $shippingAddress->getStreet()));
            $cartShippingAddress->setCity($shippingAddress->getCity());
            $cartShippingAddress->setState($shippingAddress->getRegion());
            $cartShippingAddress->setCountry($shippingAddress->getCountryId());
            $cartShippingAddress->setZipcode($shippingAddress->getPostcode());
            $cartShippingAddress->save();
            $this->shippingAddress = $cartShippingAddress;
            return $this;
        }
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
