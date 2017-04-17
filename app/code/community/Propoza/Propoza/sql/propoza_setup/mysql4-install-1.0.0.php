<?php

$installer = $this;
$installer->startSetup();


/*$installer->getConnection()->addColumn($installer->getTable('sales/quote'),
    'proposal_coupon',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'length' => 255,
        'comment' => 'Proposal coupon'
    )
);*/

$installer->getConnection()->addColumn($installer->getTable('salesrule/rule'),
    'propoza_quote_id',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        /*'length' => 255,*/
        'comment' => 'Propoza quote id'
    )
);

$installer->endSetup();
