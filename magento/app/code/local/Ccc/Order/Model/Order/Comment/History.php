<?php
class Ccc_Order_Model_Order_Comment_History extends Mage_Core_Model_Abstract
{
    const STATUS_PENDING = 'pending';
    const STATUS_HOLD = 'hold';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_PLACED = 'placed';

    protected function _construct()
    {
        $this->_init('order/order_comment_history');
    }

    public function getStatusOptions()
    {
        return [
            'placed' => self::STATUS_PLACED,
            'pending' => self::STATUS_PENDING,
            'hold' => self::STATUS_HOLD,
            'success' => self::STATUS_SUCCESS,
            'failed' => self::STATUS_FAILED,
        ];
    }
}
