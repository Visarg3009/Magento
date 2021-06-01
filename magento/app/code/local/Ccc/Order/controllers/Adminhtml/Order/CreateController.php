<?php
class Ccc_Order_Adminhtml_Order_CreateController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Order'))->_title($this->__('Orders'))->_title($this->__('New Order'));
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

    protected function makeResponse($block, $message = null)
    {
        $response = [
            'status' => 'success',
            'message' => 'this is grid action.',
            'element' => [
                [
                    'selector' => '#contentHtml',
                    'html' => $block
                ],
                [
                    'selector' => '#messageHtml',
                    'html' => $message
                ]
            ]
        ];
        header("Content-Type: application/json");
        echo json_encode($response);
    }

    public function getCart()
    {
        $customerId = $this->getRequest()->getParam('customer_id');
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getId()) {
            throw new Exception("Invalid Customer");
        }

        $cart = Mage::getModel('order/cart')->load($customerId, 'customer_id');
        if ($cart->getData()) {
            return $cart;
        }

        $cart = Mage::getModel('order/cart');
        $cart->setCustomerId($customerId);
        $cart->setCustomerGroupId($customer->getGroupId());
        $cart->setCustomerName($customer->getName());
        $cart->setCustomerEmail($customer->getEmail());
        $cart->setTotal(0.00);
        $cart->setCreatedAt(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
        $cart->setUpdatedAt(Mage::getModel('core/date')->date('Y-m-d H:i:s'));
        $cart->save();
        return $cart;
    }

    public function newAction()
    {
        try {
            $cart = $this->getCart();
            $this->updateCartItemPrice();
            $this->updateCartTotal($cart);

            $this->loadLayout();
            $this->getLayout()->getBlock('main')->setCart($cart);
            $this->_setActiveMenu('order');
            $this->_title('New Order');
            $block = $this->getLayout()->getBlock('content')->toHtml();
            $message = $this->getLayout()->getBlock('messages')->toHtml();
            $this->makeResponse($block, $message);
            //$this->renderLayout();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
            return;
        }
    }

    public function neworderAction()
    {
        try {
            $cart = $this->getCart();
            $this->updateCartItemPrice();
            $this->updateCartTotal($cart);

            $this->loadLayout();
            $this->getLayout()->getBlock('main')->setCart($cart);
            $this->_setActiveMenu('order');
            $this->_title('New Order');
            $this->renderLayout();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
            return;
        }
    }

    public function massAdditionAction()
    {
        try {

            $productIds = $this->getRequest()->getParam('product');

            if (!is_array($productIds)) {
                $this->_getSession()->addError($this->__('Please select product(s).'));
            } else {
                if (!empty($productIds)) {
                    $cart = $this->getCart();

                    foreach ($productIds as $productId) {
                        $product = Mage::getSingleton('catalog/product')->load($productId);
                        $cart->addItemToCart($product);
                    }
                    $this->updateCartTotal();
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been Added Successfully.', count($productIds))
                    );
                }
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/neworder', ['_current' => true]);
    }

    public function updateCartAction()
    {
        try {
            $itemData = $this->getRequest()->getPost('data');
            if (!$itemData) {
                throw new Exception("No item found!");
            }

            $cartItem = Mage::getModel('order/cart_item');
            foreach ($itemData as $key => $value) {
                $cartItem->load($key);
                if ($value['quantity'] == 0) {
                    $cartItem->delete();
                    continue;
                }
                $cartItem->setPrice($value['price'])->setQuantity($value['quantity'])->save();
            }
            $this->updateCartItemPrice();
            $this->updateCartTotal();
            $this->_getSession()->addSuccess(
                $this->__('Cart has been successfully updated.')
            );
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/new', ['_current' => true]);
    }

    public function updateCartItemPrice()
    {
        $cartItems = $this->getCart()->getItems();
        if ($cartItems->getData()) {
            foreach ($cartItems as $cartItem) {
                $product = Mage::getModel('catalog/product')->load($cartItem->getProductId());
                if ($product->getPrice() != $cartItem->getBasePrice()) {
                    $cartItem->setBasePrice($product->getPrice())
                        ->setPrice($product->getPrice());
                    $cartItem->save();
                }
            }
        }
    }

    public function updateCartTotal($cart = null)
    {
        $total = 0.0;
        if (!$cart) {
            $cart = $this->getCart();
        }
        $cartItems = $cart->getItems();
        if ($cartItems->getData()) {
            foreach ($cartItems as $cartItem) {
                $total += $cartItem->getPrice() * $cartItem->getQuantity();
            }
            $cart->setTotal($total);
            $cart->save();
        } else {
            $cart->setTotal($total);
            $cart->save();
        }
        return $total;
    }

    public function deleteAction($cartItemId = null)
    {
        try {
            $cartItem = Mage::getModel('order/cart_item');
            $itemId = $this->getRequest()->getParam('id');
            if ($itemId) {
                throw new Exception("Id not found!");
            }
            $cartItem->load($itemId);
            if (!$cartItem->getData()) {
                throw new Exception("Item does not exist!");
            }
            if (!$cartItem->delete()) {
                throw new Exception("Item does not delete successfully!");
            }
            $this->_getSession()->addSuccess(
                $this->__('Item has been successfully deleted.')
            );
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/new', ['customer_id' => $this->getCart()->getCustomerId()]);
    }

    public function saveBillingAddressAction()
    {
        try {
            $billing = $this->getRequest()->getPost();
            $cart = $this->getCart();
            $customer = $cart->getCustomer();

            $billingAddress = $cart->getBillingAddress();
            $billingAddress->addData($billing);
            $billingAddress->setAddressType(Ccc_Order_Model_Cart_Address::ADDRESS_TYPE_BILLING);
            $billingAddress->setCartId($cart->getId());
            $billingAddress->save();

            if ($this->getRequest()->getPost('saveInBillingAddress')) {
                $customerBillingAddress = $customer->getDefaultBillingAddress();
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
                    $customerBillingAddress->setFirstname($billing['name']);
                    $customerBillingAddress->setParentId($customer->getId());
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
        }
        $this->_redirect('*/*/new', ['_current' => true]);
    }

    public function saveShippingAddressAction()
    {
        try {
            $cart = $this->getCart();
            $customer = $cart->getCustomer();
            $cartBillingAddress = $cart->getBillingAddress();
            $cartShippingAddress = $cart->getShippingAddress();

            $flag = $this->getRequest()->getPost('sameAsBilling');
            if ($flag) {
                $data = $cartBillingAddress->getData();
                if (!$data) {
                    throw new Exception("No Billing Address Found! Please Fill Billing Address First.");
                }
                unset($data['address_id']);
                $cartShippingAddress->addData($data);
                $cartShippingAddress->setAddressType(Ccc_Order_Model_Cart_Address::ADDRESS_TYPE_SHIPPING);
                $cartShippingAddress->setCartId($cart->getId());
                $cartShippingAddress->setSameAsBilling(1);
                $cartShippingAddress->save();

                if ($this->getRequest()->getPost('saveInShippingAddress')) {
                    $customerShippingAddress = $customer->getDefaultShippingAddress();

                    if ($customerShippingAddress) {
                        $customerShippingAddress->setFirstname($cartBillingAddress->getName());
                        $customerShippingAddress->setStreet($cartBillingAddress->getAddress());
                        $customerShippingAddress->setCity($cartBillingAddress->getCity());
                        $customerShippingAddress->setRegion($cartBillingAddress->getState());
                        $customerShippingAddress->setCountryId($cartBillingAddress->getCountry());
                        $customerShippingAddress->setPostcode($cartBillingAddress->getZipcode());
                        $customerShippingAddress->save();
                    } else {
                        $customerShippingAddress = Mage::getModel('customer/address');
                        $customerShippingAddress->setFirstname($cartBillingAddress->getName());
                        $customerShippingAddress->setParentId($customer->getId());
                        $customerShippingAddress->setStreet($cartBillingAddress->getAddress());
                        $customerShippingAddress->setCity($cartBillingAddress->getCity());
                        $customerShippingAddress->setRegion($cartBillingAddress->getState());
                        $customerShippingAddress->setCountryId($cartBillingAddress->getCountry());
                        $customerShippingAddress->setPostcode($cartBillingAddress->getZipcode());
                        $customerShippingAddress->setIsDefaultShipping(1);
                        $customerShippingAddress->save();
                    }
                }
            } else {
                $shipping = $this->getRequest()->getPost();

                $cartShippingAddress->addData($shipping);
                $cartShippingAddress->setAddressType(Ccc_Order_Model_Cart_Address::ADDRESS_TYPE_SHIPPING);
                $cartShippingAddress->setCartId($cart->getId());
                $cartShippingAddress->setSameAsBilling(0);
                $cartShippingAddress->save();

                if ($this->getRequest()->getPost('saveInShippingAddress')) {
                    $customerShippingAddress = $customer->getDefaultShippingAddress();

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
                        $customerShippingAddress->setFirstname($shipping['name']);
                        $customerShippingAddress->setParentId($customer->getId());
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
        }
        $this->_redirect('*/*/new', ['_current' => true]);
    }

    public function savePaymentAction()
    {
        try {
            $paymentCode = $this->getRequest()->getPost('paymentMethod');
            $cart = $this->getCart();
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
        $this->_redirect('*/*/new', ['_current' => true]);
    }

    public function saveShipmentAction()
    {
        try {
            $shipmentData = $this->getRequest()->getPost('shipmentMethod');
            $cart = $this->getCart();
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
        $this->_redirect('*/*/new', ['_current' => true]);
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
        }
        $this->_redirect('*/*/newOrder', ['customer_id' => $this->getCart()->getCustomerId()]);
    }
}
