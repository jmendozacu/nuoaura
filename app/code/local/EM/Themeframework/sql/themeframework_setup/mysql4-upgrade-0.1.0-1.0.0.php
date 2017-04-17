<?php
$installer = $this;
$installer->startSetup();
$pathFile = Mage::getBaseDir('var').DS.'upgrade_theme_framework_1.0.0.txt';
if(file_exists($pathFile)){
    echo 'Upgrading EM Theme Framework, please come back in some minutes ...';
    exit;
}
file_put_contents($pathFile,'Upgrading Theme Framework');
if($installer->tableExists($installer->getTable('themeframework/area'))){
    $installer->getConnection()->addColumn($installer->getTable('themeframework/area'), 'name',
    'varchar(255) DEFAULT NULL AFTER `area_id`');
}

$installer->run("
SET FOREIGN_KEY_CHECKS=0;
");
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


/* Init active config path */
Mage::getModel('themeframework/theme')->initActivePath();
unlink($pathFile);
$installer->endSetup();
?>