<?php
/**
 * EMThemes
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the framework to newer
 * versions in the future. If you wish to customize the framework for your
 * needs please refer to http://www.emthemes.com/ for more information.
 *
 * @category    EM
 * @package     EM_ThemeFramework
 * @copyright   Copyright (c) 2012 CodeSpot JSC. (http://www.emthemes.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author      Giao L. Trinh (giao.trinh@emthemes.com)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$pathFile = Mage::getBaseDir('var').DS.'install_theme_framework.txt';
if(file_exists($pathFile)){
    echo 'Installing EM Theme Framework, please come back in some minutes ...';
    exit;
}
file_put_contents($pathFile,'Installing Theme Framework');
/**
 * Create table 'themeframework/area'
 */
if(!$installer->tableExists($installer->getTable('themeframework/area'))){
$table = $installer->getConnection()
    ->newTable($installer->getTable('themeframework/area'))
    ->addColumn('area_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Area ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Layout Name')
    ->addColumn('package_theme', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(
        ), 'Package Theme')
	->addColumn('layout', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(
        ), 'Layout')
	->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Area Content')
    ->addColumn('creation_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Area Creation Time')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Area Modification Time')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Area Active')
    ->setComment('EM ThemeFramework Area Table');
$installer->getConnection()->createTable($table);
}

/**
 * Create table 'cms/block_store'
 */
if(!$installer->tableExists($installer->getTable('themeframework/area_store'))){
$table = $installer->getConnection()
    ->newTable($installer->getTable('themeframework/area_store'))
    ->addColumn('area_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Area ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store ID')
    ->addIndex($installer->getIdxName('themeframework/area_store', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('themeframework/area_store', 'area_id', 'themeframework/area', 'area_id'),
        'area_id', $installer->getTable('themeframework/area'), 'area_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('themeframework/area_store', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('EM ThemeFramework Area To Store Linkage Table');
$installer->getConnection()->createTable($table);
}

/**
 * Create table 'themeframework/page'
 */
if(!$installer->tableExists($installer->getTable('themeframework/page'))){
	$table = $installer->getConnection()
		->newTable($installer->getTable('themeframework/page'))
		->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
			'identity'  => true,
			'nullable'  => false,
			'primary'   => true,
			), 'Area ID')
		->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
			), 'Title')
		->addColumn('handle', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(
			), 'Handle')	
		->addColumn('custom_handle', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(
			), 'Custom Handle')	
		->addColumn('layout', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(
			), 'Layout')
		->addColumn('layout_update_xml', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
			), 'Layout Update Xml')
		->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
			'nullable'  => false,
			'default'   => '1',
			), 'Status')
		->addColumn('sort', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'nullable'  => false,
			'default'   => '0',
			), 'Sort')	
		->setComment('EM ThemeFramework Page Table');
	$installer->getConnection()->createTable($table);
}

/**
 * Create table 'themeframework/page_store'
 */
if(!$installer->tableExists($installer->getTable('themeframework/page_store'))){ 
	$table = $installer->getConnection()
		->newTable($installer->getTable('themeframework/page_store'))
		->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
			'nullable'  => false,
			'primary'   => true,
			), 'Area ID')
		->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
			'unsigned'  => true,
			'nullable'  => false,
			'primary'   => true,
			), 'Store ID')
		->addIndex($installer->getIdxName('themeframework/page_store', array('store_id')),
			array('store_id'))
		->addForeignKey($installer->getFkName('themeframework/page_store', 'page_id', 'themeframework/area', 'area_id'),
			'page_id', $installer->getTable('themeframework/page'), 'page_id',
			Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
		->addForeignKey($installer->getFkName('themeframework/page_store', 'store_id', 'core/store', 'store_id'),
			'store_id', $installer->getTable('core/store'), 'store_id',
			Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
		->setComment('EM ThemeFramework Page To Store Linkage Table');
	$installer->getConnection()->createTable($table);
}

/* Create table 'themeframework/managethemes' */
if(!$installer->tableExists($installer->getTable('themeframework/theme'))){
	$table = $installer->getConnection()
		->newTable($installer->getTable('themeframework/theme'))
		->addColumn('theme_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'identity' => true,
			'nullable' => false,
			'primary' => true,
		), 'ManageThemes ID')
		->addColumn('theme_name', Varien_Db_Ddl_Table::TYPE_TEXT,255, array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '',
		), 'Theme Name')
		->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '',
		), 'Identifier')
		->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		), 'Created Date')
		->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		), 'Updated Date')
		->addColumn('base_theme', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '',
		), 'Base Theme')
		->addColumn('package', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '',
		), 'Package')
		->addColumn('template', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '',
		), 'Templates Folder')
		->addColumn('layout', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '',
		), 'Layout Folder')
		->addColumn('skin', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '',
		), 'Skin Folder')
		->addColumn('default_theme', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '',
		), 'Default Theme')
		->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '',
		), 'Thumbnail Image')
		->addColumn('is_clone', Varien_Db_Ddl_Table::TYPE_SMALLINT,null, array(
			'unsigned'  => true,
			'nullable'  => false,
		), 'Is Clone')
		->addColumn('is_import', Varien_Db_Ddl_Table::TYPE_SMALLINT,null, array(
			'unsigned'  => true,
			'nullable'  => true,
		), 'Check imported sample data')
		->addColumn('config_json', Varien_Db_Ddl_Table::TYPE_TEXT,'64k', array( ), 'Identifier')
		->addColumn('excluded_blocks', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
			'unsigned'  => false,
			'nullable'  => true,
		), 'Excluded Blocks from Parent Theme')		
		/*->addIndex(
			$installer->getIdxName('themeframework/theme',
				array('identifier'),
				Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
			),
			array('identifier'),
			array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))*/
		->setComment('Theme Configuration');
	$installer->getConnection()->createTable($table);
}

if(!$installer->tableExists($installer->getTable('themeframework/design_change_theme'))){
    $table = $installer->getConnection()
        ->newTable($installer->getTable('themeframework/design_change_theme'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ), 'ID')
        ->addColumn('design_change_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Design Change Id')
        ->addColumn('theme_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
        ), 'Theme Id')
        ->addForeignKey($installer->getFkName('themeframework/design_change_theme', 'theme_id', 'themeframework/theme', 'theme_id'),
            'theme_id', $installer->getTable('themeframework/theme'), 'theme_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey($installer->getFkName('themeframework/design_change_theme', 'design_change_id', 'core/design_change', 'design_change_id'),
            'design_change_id', $installer->getTable('core/design_change'), 'design_change_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('Design change theme');
    $installer->getConnection()->createTable($table);
}

/* Init active config path */
Mage::getModel('themeframework/theme')->initActivePath();

// Check if the table already exists
if ($installer->getConnection()->isTableExists($this->getTable('megamenupro')) != true) {
	/* install megamenu table */
	$installer->run("
		CREATE TABLE IF NOT EXISTS {$this->getTable('megamenupro')} (
	  `megamenupro_id` int(11) unsigned NOT NULL auto_increment, 
	  `name` varchar(150) NOT NULL default '',
	  `identifier` varchar(255) NOT NULL default '',
	  `description` text NOT NULL default '',
	  `type` smallint(6) NOT NULL default '0',
	  `content` longtext NOT NULL default '',
	  `css_class` varchar(255) NULL,
	  `status` smallint(6) NOT NULL default '0',
	  `created_time` datetime NULL,
	  `update_time` datetime NULL,
	  PRIMARY KEY (`megamenupro_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	");
}
else
{
	$installer->run("
		ALTER TABLE `{$this->getTable('megamenupro')}` CHANGE `content` `content` LONGTEXT NULL DEFAULT NULL
	");

	$installer->getConnection()->addColumn(
		$installer->getTable('megamenupro'),
		'identifier',
		'VARCHAR(100) NULL'
	);

	$installer->getConnection()->addColumn(
		$installer->getTable('megamenupro'),
		'description',
		array(
			'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
			'length'     => '2M',
			'comment'   => 'MegaMenu Description'
		)
	);

}

/**
* Add Data: move to theme setting 
*/
unlink($pathFile);
$installer->endSetup();