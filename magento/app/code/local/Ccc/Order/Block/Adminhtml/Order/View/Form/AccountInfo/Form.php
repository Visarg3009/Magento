<?php
class Ccc_Order_Block_Adminhtml_Order_View_Form_AccountInfo_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected $order = null;

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

        $fieldset->addField('group_id', 'select', [
            'name' => 'group_id',
            'label' => 'Group',
            'required' => true,
            'values' => Mage::getModel('customer/group')->getCollection()->toOptionArray()
        ]);

        $fieldset->addField('email', 'text', [
            'name' => 'accountInfo[email]',
            'label' => 'Email',
        ]);

        if (Mage::registry('accountInfo_data')) {
            $form->setValues(Mage::registry('accountInfo_data')->getData());
        }
        return parent::_prepareForm();
    }
}