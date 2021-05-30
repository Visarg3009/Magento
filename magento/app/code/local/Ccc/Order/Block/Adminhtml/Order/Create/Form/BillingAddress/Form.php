<?php
class Ccc_Order_Block_Adminhtml_Order_Create_Form_BillingAddress_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected $cart = null;
    protected $billingAddress = null;

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

    public function getCountryOptions()
    {
        return Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
    }

    public function setBillingAddress($billingAddress = null)
    {
        $address = $this->getCart()->getBillingAddress();

        if ($address->getData()) {
            $this->billingAddress = $address;
            return $this;
        }

        $billingAddress = $this->getCart()->getCustomer()->getDefaultBillingAddress();
        if ($billingAddress) {
            $cartBillingAddress = $address;
            $cartBillingAddress->setCartId($this->getCart()->getId());
            $cartBillingAddress->setFirstName($billingAddress->getFirstname());
            $cartBillingAddress->setLastName($billingAddress->getLastname());
            $cartBillingAddress->setAddressType(Ccc_Order_Model_Cart_Address::ADDRESS_TYPE_BILLING);
            $cartBillingAddress->setAddress(implode(' ', $billingAddress->getStreet()));
            $cartBillingAddress->setCity($billingAddress->getCity());
            $cartBillingAddress->setState($billingAddress->getRegion());
            $cartBillingAddress->setCountry($billingAddress->getCountryId());
            $cartBillingAddress->setZipcode($billingAddress->getPostcode());
            $cartBillingAddress->save();
            $this->billingAddress = $cartBillingAddress;
            return $this;
        }
        $this->billingAddress = $address;
        return $this;
    }

    public function getBillingAddress()
    {
        if (!$this->billingAddress) {
            $this->setBillingAddress();
        }
        return $this->billingAddress;
    }
}
