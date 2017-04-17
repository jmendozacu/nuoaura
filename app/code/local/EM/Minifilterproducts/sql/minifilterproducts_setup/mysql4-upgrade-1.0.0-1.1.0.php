<?php
$installer = $this;
$installer->startSetup();
if($attributeId = $installer->getAttributeId('catalog_product', 'em_featured')) {
    $installer->updateAttribute('catalog_product', $attributeId, array(
        'group' => 'General',
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Featured Product',
        'input' => 'boolean',
        'class' => '',
        'source' => 'eav/entity_attribute_source_boolean',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '0',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
        'is_configurable' => false,
        'used_for_promo_rules' => true
    ));
}

if($attributeId = $installer->getAttributeId('catalog_product', 'em_deal')) {
    $installer->addAttribute('catalog_product', 'em_deal', array(
        'group' => 'General',
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Special Deal',
        'input' => 'boolean',
        'class' => '',
        'source' => 'eav/entity_attribute_source_boolean',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '0',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
        'is_configurable' => false,
        'used_for_promo_rules' => 1
    ));
}

if($attributeId = $installer->getAttributeId('catalog_product', 'em_hot')) {
    $installer->addAttribute('catalog_product', 'em_hot', array(
        'group' => 'General',
        'type' => 'int',
        'backend' => '',
        'frontend' => '',
        'label' => 'Hot Product',
        'input' => 'boolean',
        'class' => '',
        'source' => 'eav/entity_attribute_source_boolean',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible' => true,
        'required' => false,
        'user_defined' => true,
        'default' => '0',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'unique' => false,
        'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
        'is_configurable' => false,
        'used_for_promo_rules' => 1
    ));
}
$installer->endSetup();