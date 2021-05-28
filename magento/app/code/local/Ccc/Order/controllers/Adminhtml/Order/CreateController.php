<?php
class Ccc_Order_Adminhtml_Order_CreateController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct()
    {
        $this->setUsedModuleName('Ccc_Order');

        // During order creation in the backend admin has ability to add any products to order
        // Mage::helper('catalog/product')->setSkipSaleableCheck(true);
    }

    // protected function _getSession()
    // {
    //     return Mage::getSingleton('adminhtml/session_quote');
    // }

    protected function _getQuote()
    {
        return $this->_getSession()->getQuote();
    }

    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }

    public function getCart()
    {
        return Mage::registry('order_cart');
    }

    public function _initSession()
    {
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $this->_getSession()->setCustomerId((int) $customerId);
        }

        if ($storeId = $this->getRequest()->getParam('store_id')) {
            $this->_getSession()->setStoreId((int) $storeId);
        }

        if ($currencyId = $this->getRequest()->getParam('currency_id')) {
            $this->_getSession()->setCurrencyId((string) $currencyId);
            $this->_getOrderCreateModel()->setRecollect(true);
        }

        Mage::dispatchEvent(
            'create_order_session_quote_initialized',
            array('session_quote' => $this->_getSession())
        );
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Order'))->_title($this->__('Orders'))->_title($this->__('New Order'));
        // $this->_initSession();
        $this->loadLayout();

        $this->_setActiveMenu('order/order')
            ->renderLayout();
    }

    public function customerGridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('order/adminhtml_order_create_customer_grid')->toHtml());
    }

    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('order/adminhtml_order_create_form_product_grid')->toHtml());
    }

    public function newAction()
    {
        $this->loadLayout();
        $customerId = $this->getRequest()->getParam('customer_id');
        if ($customerId) {
            try {
                $customer = Mage::getModel('customer/customer')->load($customerId);
                if (!$customer->getId()) {
                    $this->_getSession()->addError(Mage::helper('order')->__('This customer has no longer exists.'));
                    $this->_redirect('*/*/');
                    return;
                }
                Mage::register('accountInfo_data', $customer);
                $cart = Mage::getModel('order/cart');
                $cart->load($customerId, 'customer_id');
                if (!$cart->getData()) {
                    $cart->setCustomerId($customerId);
                    $cart->setCustomerGroupId($customer->getGroupId());
                    $cart->setCustomerName($customer->getName());
                    $cart->setCreatedAt(time());
                    if (!$cart->save()) {
                        $this->_getSession()->addError(Mage::helper('order')->__('Cart cannot saved successfully.'));
                        $this->_redirect('*/*/');
                        return;
                    }
                }
                $this->updateCartPrice($cart);
                $this->updateCartTotal($cart);
                $this->_getSession()->setData('order_cart', $cart);
                Mage::register('order_cart', $cart);
                $cartBillingAddress = Mage::getModel('order/cart_address')->getBillingAddress($cart->getId(), $customerId);
                $cartShippingAddress = Mage::getModel('order/cart_address')->getShippingAddress($cart->getId(), $customerId);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->renderLayout();
    }

    public function massAdditionAction()
    {
        $productIds = $this->getRequest()->getParam('product');

        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
        } else {
            if (!empty($productIds)) {
                try {
                    foreach ($productIds as $productId) {
                        $product = Mage::getSingleton('catalog/product')->load($productId);
                        $cartId = $this->_getSession()->getData('order_cart')->getId();
                        if (!$cartId) {
                            $this->_getSession()->addError(Mage::helper('order')->__('This cart has no longer exists.'));
                            $this->_redirect('*/*/');
                            return;
                        }

                        $cartItemModel = Mage::getModel('order/cart_item');
                        $cartItemCollection = $cartItemModel->getCollection();
                        $select = $cartItemCollection->getSelect();
                        $select->where('cart_id = ?', $cartId);
                        $select->Where('product_id = ?', $productId);

                        $cartItem = $cartItemCollection->fetchItem($select);
                        if ($cartItem) {
                            $cartItem->quantity += 1;
                            $cartItem->save();
                            continue;
                        }
                        $cartItemModel = Mage::getModel('order/cart_item');
                        $cartItemModel->setCartId($cartId);
                        $cartItemModel->setProductId($product->getId());
                        $cartItemModel->setSku($product->getSku());
                        $cartItemModel->setName($product->getName());
                        $cartItemModel->setQuantity(1);
                        $cartItemModel->setBasePrice($product->getPrice());
                        $cartItemModel->setPrice($product->getPrice());
                        $cartItemModel->setCreatedAt(time());
                        if (!$cartItemModel->save()) {
                            $this->_getSession()->addError(Mage::helper('order')->__('CartItem cannot added successfully.'));
                            $this->_redirect('*/*/');
                            return;
                        }
                    }
                    $this->updateCartPrice();
                    $this->updateCartTotal();

                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been Added Successfully.', count($productIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/new', ['customer_id' => $this->_getSession()->getData('order_cart')->getCustomerId()]);
    }

    public function updateCartTotal($cart = null)
    {
        if (!$cart) {
            $cart = $this->_getSession()->getData('order_cart');
        }
        $total = 0.0;
        $cartId = $cart->getId();

        $cartItem = Mage::getModel('order/cart_item')->getCollection()->addFieldToFilter('cart_id', ['eq' => $cartId]);
        if ($cartItem->getData()) {
            foreach ($cartItem as $cartItem) {
                $total += $cartItem->getPrice() * $cartItem->getQuantity();
            }
            $cart = Mage::getModel('order/cart')->load($cartId);
            $cart->setTotal($total);
            $cart->setUpdatedAt(time());
            $cart->save();
        } else {
            $cart = Mage::getModel('order/cart')->load($cartId);
            $cart->setTotal($total);
            $cart->setUpdatedAt(time());
            $cart->save();
        }
        return $total;
    }

    public function updateCartAction()
    {
        $itemData = $this->getRequest()->getPost('data');

        $cartItem = Mage::getModel('order/cart_item');
        foreach ($itemData as $key => $value) {
            $cartItem->load($key);
            if ($value['quantity'] == 0) {
                $cartItem->delete();
                continue;
            }
            $cartItem->setPrice($value['price'])->setQuantity($value['quantity'])->save();
        }
        $this->_getSession()->addSuccess(
            $this->__('Cart has been successfully updated.')
        );
        $this->_redirect('*/*/new', ['customer_id' => $this->_getSession()->getData('order_cart')->getCustomerId()]);
        $this->updateCartPrice();
        $this->updateCartTotal();
    }

    public function updateCartPrice($cart = null)
    {
        if (!$cart) {
            $cart = $this->_getSession()->getData('order_cart');
        }
        $cartItems = Mage::getModel('order/cart_item')->getCollection()
            ->addFieldToFilter('cart_id', ['eq' => $cart->getId()]);
        foreach ($cartItems as $cartItem) {
            $product = Mage::getModel('catalog/product')->load($cartItem->getProductId());
            if ($product->getPrice() != $cartItem->getBasePrice()) {
                $cartItem->setBasePrice($product->getPrice())
                    ->setPrice($product->getPrice());
                $cartItem->save();
            }
        }
    }

    public function deleteAction($cartItemId = null)
    {
        $cartItem = Mage::getModel('order/cart_item');
        $itemId = $this->getRequest()->getParam('id');
        if ($cartItemId) {
            $itemId = $cartItemId;
        }
        $cartItem = $cartItem->load($itemId);
        if ($cartItem) {
            $cartItem->delete($itemId);
        }
        if ($cartItemId) {
            return true;
        } else {
            $this->_getSession()->addSuccess(
                $this->__('Item has been successfully deleted.')
            );
        }
    }

    public function saveBillingAddressAction()
    {
        try {
            $billing = $this->getRequest()->getPost();
            $cartId = $this->_getSession()->getData('order_cart')->getId();
            $cartAddress = Mage::getModel('order/cart_address');
            if ($billingAddress = Mage::getModel('order/cart_address')->getBillingAddress($cartId)) {
                $id = $billingAddress->getId();
                $cartAddress->load($id);
            }
            $cartAddress->addData($billing);
            $cartAddress->addressType = 'billing';
            $cartAddress->cartId = $cartId;
            $cartAddress->save();

            if ($this->getRequest()->getPost('saveInBillingAddress')) {
                $customerId = $this->_getSession()->getData('order_cart')->getCustomerId();
                $customerBillingAddress = Mage::getModel('customer/customer')->load($customerId)->getDefaultBillingAddress();
                if ($customerBillingAddress) {
                    $customerBillingAddress->setFirstname($billing['name']);
                    $customerBillingAddress->setStreet($billing['address']);
                    $customerBillingAddress->setCity($billing['city']);
                    $customerBillingAddress->setRegion($billing['state']);
                    $customerBillingAddress->setCountryId($billing['country']);
                    $customerBillingAddress->setPostcode($billing['zipcode']);
                    $customerBillingAddress->save();
                } else {
                    $customerBillingAddress = Mage::getModel('customer/address');
                    $customerBillingAddress->setEntityTypeId($customerBillingAddress->getEntityTypeId());
                    $customerBillingAddress->setName($billing['name']);
                    $customerBillingAddress->setFirstname($billing['name']);
                    $customerBillingAddress->setParentId($customerId);
                    $customerBillingAddress->setCustomerId($customerId);
                    $customerBillingAddress->setStreet($billing['address']);
                    $customerBillingAddress->setCity($billing['city']);
                    $customerBillingAddress->setRegion($billing['state']);
                    $customerBillingAddress->setCountryId($billing['country']);
                    $customerBillingAddress->setPostcode($billing['zipcode']);
                    $customerBillingAddress->setIsDefaultBilling(1);
                    $customerBillingAddress->save();
                }
            }
            $this->_getSession()->addSuccess(
                $this->__('Billing Address has been saved successfully.')
            );
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/new', ['customer_id' => $this->_getSession()->getData('order_cart')->getCustomerId()]);
        }
        $this->_redirect('*/*/new', ['customer_id' => $this->_getSession()->getData('order_cart')->getCustomerId()]);
    }

    public function saveShippingAddressAction()
    {
        try {
            $flag = $this->getRequest()->getPost('sameAsBilling');
            if ($flag) {
                $cartId = $this->_getSession()->getData('order_cart')->getId();

                $billing = Mage::getModel('order/cart_address')->getBillingAddress($cartId);
                $cartAddress = Mage::getModel('order/cart_address');
                if ($shipping = Mage::getModel('order/cart_address')->getShippingAddress($cartId)) {
                    $id = $shipping->getId();
                    $data = $billing->getData();
                    unset($data['address_id']);
                    $cartAddress->load($id);
                }

                $cartAddress->addData($data);
                $cartAddress->addressType = 'shipping';
                $cartAddress->cartId = $cartId;
                $cartAddress->setSaveAsBilling(1);
                $cartAddress->save();
                if ($this->getRequest()->getPost('saveInShippingAddress')) {
                    $customerId = $this->_getSession()->getData('order_cart')->getCustomerId();

                    $customerShippingAddress = Mage::getModel('customer/customer')->load($customerId)->getDefaultShippingAddress();

                    if ($customerShippingAddress) {
                        $customerShippingAddress->setFirstname($billing->getName());
                        $customerShippingAddress->setStreet($billing->getAddress());
                        $customerShippingAddress->setCity($billing->getCity());
                        $customerShippingAddress->setRegion($billing->getState());
                        $customerShippingAddress->setCountryId($billing->getCountry());
                        $customerShippingAddress->setPostcode($billing->getZipcode());
                        $customerShippingAddress->save();
                    } else {
                        $customerShippingAddress = Mage::getModel('customer/address');
                        $customerShippingAddress->setEntityTypeId($customerShippingAddress->getEntityTypeId());
                        $customerShippingAddress->setName($billing->getName());
                        $customerShippingAddress->setFirstname($billing->getName());
                        $customerShippingAddress->setParentId($customerId);
                        $customerShippingAddress->setCustomerId($customerId);
                        $customerShippingAddress->setStreet($billing->getAddress());
                        $customerShippingAddress->setCity($billing->getCity());
                        $customerShippingAddress->setRegion($billing->getState());
                        $customerShippingAddress->setCountryId($billing->getCountry());
                        $customerShippingAddress->setPostcode($billing->getZipcode());
                        $customerShippingAddress->setIsDefaultShipping(1);
                        $customerShippingAddress->save();
                    }
                }
            } else {
                $shipping = $this->getRequest()->getPost();
                $cartId = $this->_getSession()->getData('order_cart')->getId();

                $cartAddress = Mage::getModel('order/cart_address');
                if ($shippingAddress = Mage::getModel('order/cart_address')->getShippingAddress($cartId)) {
                    $id = $shippingAddress->getId();
                    $cartAddress->load($id);
                }

                $cartAddress->addData($shipping);
                $cartAddress->addressType = 'shipping';
                $cartAddress->cartId = $cartId;
                $cartAddress->setSaveAsBilling(0);
                $cartAddress->save();

                if ($this->getRequest()->getPost('saveInShippingAddress')) {
                    $customerId = $this->_getSession()->getData('order_cart')->getCustomerId();
                    $customerShippingAddress = Mage::getModel('customer/customer')->load($customerId)->getDefaultShippingAddress();

                    if ($customerShippingAddress) {
                        $customerShippingAddress->setFirstname($shipping['name']);
                        $customerShippingAddress->setStreet($shipping['address']);
                        $customerShippingAddress->setCity($shipping['city']);
                        $customerShippingAddress->setRegion($shipping['state']);
                        $customerShippingAddress->setCountryId($shipping['country']);
                        $customerShippingAddress->setPostcode($shipping['zipcode']);
                        $customerShippingAddress->save();
                    } else {
                        $customerShippingAddress = Mage::getModel('customer/address');
                        $customerShippingAddress->setEntityTypeId($customerShippingAddress->getEntityTypeId());
                        $customerShippingAddress->setName($shipping['name']);
                        $customerShippingAddress->setFirstname($shipping['name']);
                        $customerShippingAddress->setParentId($customerId);
                        $customerShippingAddress->setCustomerId($customerId);
                        $customerShippingAddress->setStreet($shipping['address']);
                        $customerShippingAddress->setCity($shipping['city']);
                        $customerShippingAddress->setRegion($shipping['state']);
                        $customerShippingAddress->setCountryId($shipping['country']);
                        $customerShippingAddress->setPostcode($shipping['zipcode']);
                        $customerShippingAddress->setIsDefaultShipping(1);
                        $customerShippingAddress->save();
                    }
                }
            }
            $this->_getSession()->addSuccess(
                $this->__('Shipping Address has been saved successfully.')
            );
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/new', ['customer_id' => $this->_getSession()->getData('order_cart')->getCustomerId()]);
            return;
        }
        $this->_redirect('*/*/new', ['customer_id' => $this->_getSession()->getData('order_cart')->getCustomerId()]);
    }

    public function savePaymentAction()
    {
        try {
            $paymentCode = $this->getRequest()->getPost('paymentMethod');
            $cart = $this->_getSession()->getData('order_cart');
            $cart->setPaymentMethodCode($paymentCode);
            $cart->setUpdatedAt(time());
            if ($cart->save()) {
                $this->_getSession()->addSuccess(
                    $this->__('Payment Method has been saved successfully.')
                );
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/new', ['customer_id' => $this->_getSession()->getData('order_cart')->getCustomerId()]);
    }

    public function saveShipmentAction()
    {
        try {
            $shipmentData = $this->getRequest()->getPost('shipmentMethod');
            $cart = $this->_getSession()->getData('order_cart');
            $data = explode(' ', $shipmentData);
            $method = $data[0];
            $price = $data[1];

            $cart->setShippingMethodCode($method);
            $cart->setShippingAmount($price);
            $cart->setUpdatedAt(time());
            if ($cart->save()) {
                $this->_getSession()->addSuccess(
                    $this->__('Shipment Method has been saved successfully.')
                );
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/new', ['customer_id' => $this->_getSession()->getData('order_cart')->getCustomerId()]);
    }

    public function deleteItemAction()
    {
        try {

            $cartItem = Mage::getModel('order/cart_item');

            if (!($itemId = (int) $this->getRequest()->getParam('item_id')))
                throw new Exception('Id not found');

            if (!$cartItem->load($itemId)) {
                throw new Exception('item does not exist');
            }

            if (!$cartItem->delete()) {
                throw new Exception('Error in delete record', 1);
            }

            Mage::getSingleton('core/session')->addSuccess($this->__('The product has been deleted.'));
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('core/session')->addError($e->getMessage());
            return;
        }

        $this->_redirect('*/*/new', ['customer_id' => $this->_getSession()->getData('order_cart')->getCustomerId()]);
    }
}
