<?php
class Ccc_Order_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('order/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('order', $order);
        Mage::register('current_order', $order);
        return $order;
    }

    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('order/order')
            ->_addBreadcrumb($this->__('Order'), $this->__('Order'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('order/adminhtml_order_grid')->toHtml());
    }


    public function placeOrderAction()
    {
        try {
            $cart = $this->getCart();
            if ($cart->getTotal() == 0) {
                throw new Exception("No Items Avaiable in your Cart");
            }
            if (!$cart->getBillingAddress($cart->getId(), $cart->getCustomerId())->getState()) {
                throw new Exception("Please Enter Billing Address");
            }
            if (!$cart->getShippingAddress($cart->getId(), $cart->getCustomerId())->getState()) {
                throw new Exception("Please Enter Shipping Address");
            }
            if (!$cart->getPaymentMethodCode()) {
                throw new Exception("Please Select Payment Method");
            }
            if (!$cart->getShippingMethodCode()) {
                throw new Exception("Please Select Shipping Method");
            }
            $this->saveOrder();
            $this->deleteCart();
            $this->_getSession()->addSuccess($this->__('Order has been saved successfully.'));
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/adminhtml_order_create/new', ['customer_id' => $this->_getSession()->getData('order_cart')->getCustomerId()]);
            return;
        }
        $this->_redirect('*/*/');
    }

    public function getCart()
    {
        $cart = $this->_getSession()->getData('order_cart');
        $cartId = $cart->getId();
        $cart = Mage::getModel('order/cart');
        if ($cartId) {
            $cart = $cart->load($cartId);
            if (!$cart) {
                throw new \Exception("No Cart Found!");
            }
        }
        if (!$cart) {
            return false;
        }
        return $cart;
    }

    public function saveOrder()
    {
        $cart = $this->getCart();
        $customer = Mage::getModel('customer/customer')->load($cart->getCustomerId());
        $order = Mage::getModel('order/order');
        $order->setCustomerId($cart->getCustomerId());
        $order->setCustomerEmail($customer->getEmail());
        $order->setDiscount($cart->getDiscount());
        $order->setTotal($cart->getTotal());
        $order->setCustomerName($cart->getCustomerName());
        $order->setPaymentMethodCode($cart->getPaymentMethodCode());
        $order->setShippingMethodCode($cart->getShippingMethodCode());
        $order->setShippingAmount($cart->getShippingAmount());
        $order->setGrandTotal($cart->getTotal() + $cart->getShippingAmount());
        $order->setCreatedAt(time());
        $order->save();
        $this->saveOrderItem($order);
        $this->saveOrderAddresses($order);
    }

    public function saveOrderItem($order)
    {
        $cartId = $this->getCart()->getId();
        $cartItems = Mage::getModel('order/cart_item')->getCollection()->addFieldToFilter('cart_id', ['eq' => $cartId]);

        foreach ($cartItems as $cartItem) {
            $orderItem = Mage::getModel('order/order_item');
            $orderItem->setOrderId($order->getId());
            $orderItem->setProductId($cartItem->getProductId());
            $orderItem->setQuantity($cartItem->getQuantity());
            $orderItem->setBasePrice($cartItem->getBasePrice());
            $orderItem->setPrice($cartItem->getPrice());
            $orderItem->setDiscount($cartItem->getDiscount());
            $orderItem->setCreatedAt(time());
            $orderItem->save();
        }
    }

    public function saveOrderAddresses($order)
    {
        $cartId = $this->getCart()->getId();
        $cartAddresses = Mage::getModel('order/cart_address')->getCollection()->addFieldToFilter('cart_id', ['eq' => $cartId]);

        foreach ($cartAddresses as $cartAddress) {
            $orderAddress = Mage::getModel('order/order_address');
            $orderAddress->setOrderId($order->getId());
            $orderAddress->setCustomerId($order->getCustomerId());
            $orderAddress->setCartAddressId($cartAddress->getId());
            $orderAddress->setAddressType($cartAddress->getAddressType());
            $orderAddress->setAddress($cartAddress->getAddress());
            $orderAddress->setCity($cartAddress->getCity());
            $orderAddress->setState($cartAddress->getState());
            $orderAddress->setCountry($cartAddress->getCountry());
            $orderAddress->setZipcode($cartAddress->getZipcode());
            $orderAddress->save();
        }
    }

    public function deleteCart()
    {
        $cart = $this->getCart();
        $cartItems = Mage::getModel('order/cart_item')->getCollection()->addFieldToFilter('cart_id', ['eq' => $cart->getId()]);
        foreach ($cartItems as $cartItem) {
            $item = Mage::getModel('order/cart_item');
            $item->delete($cartItem->getId());
        }

        $cartAddresses = Mage::getModel('order/cart_address')->getCollection()->addFieldToFilter('cart_id', ['eq' => $cart->getId()]);
        foreach ($cartAddresses as $cartAddress) {
            $address = Mage::getModel('order/cart_address');
            $address->delete($cartAddress->getId());
        }
        $cart->delete($cart->getId());
    }
}
