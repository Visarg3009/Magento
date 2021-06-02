<?php

class Ccc_Order_Model_Resource_Order_Comment_History extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('order/order_comment_history', 'comment_id');
    }
}
