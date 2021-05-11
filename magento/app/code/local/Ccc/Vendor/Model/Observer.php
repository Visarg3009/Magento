<?php
class Ccc_Vendor_Model_Observer
{
    public function beforeLoadLayout($observer)
    {
        $loggedIn = Mage::getSingleton('vendor/session')->isLoggedIn();
        $observer->getEvent()->getLayout()->getUpdate()
            ->addHandle('vendor_logged_' . ($loggedIn ? 'in' : 'out'));
    }
}
