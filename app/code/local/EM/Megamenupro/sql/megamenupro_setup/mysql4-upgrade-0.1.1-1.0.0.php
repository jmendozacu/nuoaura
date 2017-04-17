<?php
/** @var $installer Mage_Catalog_Model_Resource_Setup */
$installer  = $this;

	$pathFile = Mage::getBaseDir('var').DS.'[EM_Megamenu]update_1-0-0.txt';
	if(file_exists($pathFile)){
		echo 'Updating EM Mega Menu version 1.0.0 , please come back in some minutes ...';
		exit;
	}
	file_put_contents($pathFile,'Updating EM Mega Menu version 1.0.0');

	$installer->run("
		ALTER TABLE `{$this->getTable('megamenupro/megamenupro')}` CHANGE `content` `content` LONGTEXT NULL DEFAULT NULL
	");

	$installer->getConnection()->addColumn(
		$installer->getTable('megamenupro/megamenupro'),
		'identifier',
		'VARCHAR(100) NULL'
	);

	$installer->getConnection()->addColumn(
		$installer->getTable('megamenupro/megamenupro'),
		'description',
		array(
			'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
			'length'     => '2M',
			'comment'   => 'Slideshow Description'
		)
	);

	Mage::getModel("megamenupro/update")->version("1.0.0");
	unlink($pathFile);

$installer->endSetup(); 