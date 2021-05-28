<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('order/order'))
    ->addColumn(
        'order_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'primary' => true,
            'nullable' => false,
        ),
        'Order Id'
    )
    ->addColumn(
        'customer_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Customer Id'
    )
    ->addColumn(
        'discount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '10,2',
        array(
            'nullable' => true,
        ),
        'Discount'
    )
    ->addColumn(
        'total',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '10,2',
        array(
            'nullable' => true,
        ),
        'Total'
    )
    ->addColumn(
        'customer_name',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array(
            'nullable' => true,
        ),
        'Customer Name'
    )
    ->addColumn(
        'customer_email',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array(
            'nullable' => true,
        ),
        'Customer Email'
    )
    ->addColumn(
        'payment_method_code',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        10,
        array(
            'nullable' => true,
        ),
        'Payment Method Code'
    )
    ->addColumn(
        'shipping_method_code',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        10,
        array(
            'nullable' => true,
        ),
        'Shipping Method Code'
    )
    ->addColumn(
        'shipping_amount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '10,2',
        array(
            'nullable' => true,
        ),
        'Shipping Amount'
    )
    ->addColumn(
        'grand_total',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '10,2',
        array(
            'nullable' => true,
        ),
        'Grand Total'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array(
            'nullable' => true,
        ),
        'Created At'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array(
            'nullable' => true,
        ),
        'Updated At'
    );
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('order/order_item'))
    ->addColumn(
        'item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'primary' => true,
            'nullable' => false,
        ),
        'Item Id'
    )
    ->addColumn(
        'order_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Order Id'
    )
    ->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Product Id'
    )
    ->addColumn(
        'quantity',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Quantity'
    )
    ->addColumn(
        'base_price',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '10,2',
        array(
            'nullable' => true,
        ),
        'Base Price'
    )
    ->addColumn(
        'price',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '10,2',
        array(
            'nullable' => true,
        ),
        'Price'
    )
    ->addColumn(
        'discount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '10,2',
        array(
            'nullable' => true,
        ),
        'Discount'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array(
            'nullable' => true,
        ),
        'Created At'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array(
            'nullable' => true,
        ),
        'Updated At'
    );
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('order/order_address'))
    ->addColumn(
        'address_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'primary' => true,
            'nullable' => false,
        ),
        'Address Id'
    )
    ->addColumn(
        'order_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Order Id'
    )
    ->addColumn(
        'customer_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Customer Id'
    )
    ->addColumn(
        'cart_address_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Cart Address Id'
    )
    ->addColumn(
        'address_type',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        20,
        array(
            'nullable' => true,
        ),
        'Address Type'
    )
    ->addColumn(
        'address',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        null,
        array(
            'nullable' => true,
        ),
        'Address'
    )
    ->addColumn(
        'city',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        null,
        array(
            'nullable' => true,
        ),
        'City'
    )
    ->addColumn(
        'state',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        null,
        array(
            'nullable' => true,
        ),
        'State'
    )
    ->addColumn(
        'country',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        null,
        array(
            'nullable' => true,
        ),
        'Country'
    )
    ->addColumn(
        'zipcode',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Zipcode'
    )
    ->addColumn(
        'bill_to_name',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Billing Name'
    )
    ->addColumn(
        'ship_to_name',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Shipping Name'
    );
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('order/cart'))
    ->addColumn(
        'cart_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'primary' => true,
            'nullable' => false,
        ),
        'Cart Id'
    )
    ->addColumn(
        'customer_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Customer Id'
    )
    ->addColumn(
        'discount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '10,2',
        array(
            'nullable' => true,
        ),
        'Discount'
    )
    ->addColumn(
        'total',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '10,2',
        array(
            'nullable' => true,
        ),
        'Total'
    )
    ->addColumn(
        'customer_group_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Customer Group Id'
    )
    ->addColumn(
        'customer_name',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array(
            'nullable' => true,
        ),
        'Customer Name'
    )
    ->addColumn(
        'payment_method_code',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        10,
        array(
            'nullable' => true,
        ),
        'Payment Method Code'
    )
    ->addColumn(
        'shipping_method_code',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        10,
        array(
            'nullable' => true,
        ),
        'Shipping Method Code'
    )
    ->addColumn(
        'shipping_amount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '10,2',
        array(
            'nullable' => true,
        ),
        'Shipping Amount'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array(
            'nullable' => true,
        ),
        'Created At'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array(
            'nullable' => true,
        ),
        'Updated At'
    );
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('order/cart_item'))
    ->addColumn(
        'item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'primary' => true,
            'nullable' => false,
        ),
        'Item Id'
    )
    ->addColumn(
        'cart_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Cart Id'
    )
    ->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Product Id'
    )
    ->addColumn(
        'sku',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array(
            'nullable' => true,
        ),
        'SKU'
    )
    ->addColumn(
        'name',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        50,
        array(
            'nullable' => true,
        ),
        'Name'
    )
    ->addColumn(
        'quantity',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Quantity'
    )
    ->addColumn(
        'base_price',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '10,2',
        array(
            'nullable' => true,
        ),
        'Base Price'
    )
    ->addColumn(
        'price',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '10,2',
        array(
            'nullable' => true,
        ),
        'Price'
    )
    ->addColumn(
        'discount',
        Varien_Db_Ddl_Table::TYPE_DECIMAL,
        '10,2',
        array(
            'nullable' => true,
        ),
        'Discount'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array(
            'nullable' => true,
        ),
        'Created At'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_DATETIME,
        null,
        array(
            'nullable' => true,
        ),
        'Updated At'
    );
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('order/cart_address'))
    ->addColumn(
        'address_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity' => true,
            'primary' => true,
            'nullable' => false,
        ),
        'Address Id'
    )
    ->addColumn(
        'cart_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Cart Id'
    )
    ->addColumn(
        'name',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        20,
        array(
            'nullable' => true,
        ),
        'Name'
    )
    ->addColumn(
        'save_in_address_book',
        Varien_Db_Ddl_Table::TYPE_TINYINT,
        4,
        array(
            'nullable' => true,
        ),
        'Svae In Address Book'
    )
    ->addColumn(
        'save_as_billing',
        Varien_Db_Ddl_Table::TYPE_TINYINT,
        4,
        array(
            'nullable' => true,
        ),
        'save As Billing'
    )
    ->addColumn(
        'address_type',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        20,
        array(
            'nullable' => true,
        ),
        'Address Type'
    )
    ->addColumn(
        'address',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        null,
        array(
            'nullable' => true,
        ),
        'Address'
    )
    ->addColumn(
        'city',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        null,
        array(
            'nullable' => true,
        ),
        'City'
    )
    ->addColumn(
        'state',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        null,
        array(
            'nullable' => true,
        ),
        'State'
    )
    ->addColumn(
        'country',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        null,
        array(
            'nullable' => true,
        ),
        'Country'
    )
    ->addColumn(
        'zipcode',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'nullable' => true,
        ),
        'Zipcode'
    );
$installer->getConnection()->createTable($table);
$installer->endSetup();
