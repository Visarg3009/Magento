<?php
class Ccc_Seller_Model_Resource_Seller extends Mage_Eav_Model_Entity_Abstract
{
    const ENTITY = 'seller';

    public function __construct()
    {
        $this->setType(self::ENTITY)
            ->setConnection('core_read', 'core_write');

        parent::__construct();
    }
}
