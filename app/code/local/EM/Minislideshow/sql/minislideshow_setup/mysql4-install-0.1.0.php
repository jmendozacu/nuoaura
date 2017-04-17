<?php

$installer = $this;

$installer->startSetup();

/**
 * Create table 'minislideshow/slider'
 */
if(!$installer->tableExists($installer->getTable('minislideshow/slider'))){
$table = $installer->getConnection()
    ->newTable($installer->getTable('minislideshow/slider'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Slideshow ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(
        ), 'Title')
    ->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        ), 'Identifier')
	->addColumn('images', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'images')
	->addColumn('slider_params', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Slideshow params')
	->addColumn('appearance', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'appearance')
	->addColumn('navigation', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'navigation')
    ->addColumn('creation_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Slideshow Creation Time')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Slideshow Modification Time')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '2',
        ), 'Is Slideshow Active')
    ->setComment('EM Minislideshow Slider Table');
$installer->getConnection()->createTable($table);
}

$installer->endSetup(); 