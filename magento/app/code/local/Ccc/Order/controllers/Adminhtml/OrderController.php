<?php
class Ccc_Order_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
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

    public function getCart()
    {
        $customerId = $this->getRequest()->getParam('customer_id');
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getId()) {
            throw new Exception("Invalid Customer");
        }

        $cart = Mage::getModel('order/cart')->load($customerId, 'customer_id');
        if (!$cart->getData()) {
            throw new Exception("No Cart Found!");
        }
        return $cart;
    }

    public function placeOrderAction()
    {
        try {
            $cart = $this->getCart();
            if ($cart->getTotal() == 0) {
                throw new Exception("No Items Avaiable in your Cart");
            }
            if (!$cart->getBillingAddress()->getData()) {
                throw new Exception("Please Enter Billing Address");
            }
            if (!$cart->getShippingAddress()->getData()) {
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

    public function saveOrder()
    {
        $cart = $this->getCart();
        $order = Mage::getModel('order/order');
        $order->setCustomerId($cart->getCustomerId());
        $order->setCustomerEmail($cart->getEmail());
        $order->setDiscount($cart->getDiscount());
        $order->setTotal($cart->getTotal());
        $order->setCustomerName($cart->getCustomerName());
        $order->setCustomerEmail($cart->getCustomerEmail());
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
        $cartItems = $this->getCart()->getItems();

        foreach ($cartItems as $cartItem) {
            $orderItem = Mage::getModel('order/order_item');
            $orderItem->setOrderId($order->getId());
            $orderItem->setProductId($cartItem->getProductId());
            $orderItem->setSku($cartItem->getSku());
            $orderItem->setName($cartItem->getName());
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
        $cartAddresses = $this->getCart()->getAddresses();

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
        $cartItems = $cart->getItems();
        foreach ($cartItems as $cartItem) {
            $cartItem->delete();
        }
        $cartAddresses = $cart->getAddresses();
        foreach ($cartAddresses as $cartAddress) {
            $cartAddress->delete();
        }
        $cart->delete();
    }

    public function getOrder()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('order/order')->load($orderId);
        if (!$order->getId()) {
            throw new Exception("Invalid Order Id");
        }

        $order = Mage::getModel('order/order')->load($orderId, 'order_id');
        if (!$order->getData()) {
            throw new Exception("No Order Found!");
        }
        return $order;
    }

    public function viewAction()
    {
        $this->loadLayout();
        $order = $this->getOrder();
        $this->getLayout()->getBlock('main')->setOrder($order);
        $this->renderLayout();
    }
}
