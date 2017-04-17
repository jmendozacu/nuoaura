<?php
$installer = $this;
$installer->addAttribute(MF_Flexibleblock_Model_Fblock::ENTITY, 'identifier', array(
    'type'               => 'varchar',
    'label'              => 'Identifier',
    'input'              => 'text',
    'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_GLOBAL,
    'required'           => true,
    'sort_order'         => 12,
    'frontend_class'     => 'validate-xml-identifier'
));
$installer->addAttribute(MF_Flexibleblock_Model_Fblock::ENTITY, 'package_theme', array(
    'type'               => 'varchar',
    'label'              => 'Package Theme',
    'input'              => 'select',
    'source'             => 'flexibleblock/fblock_attribute_source_design',
    'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
    'required'           => true,
    'sort_order'         => 55
));
$installer->addAttribute(MF_Flexibleblock_Model_Fblock::ENTITY, 'layout_handle', array(
    'type'               => 'varchar',
    'label'              => 'Layout Handle 1',
    'input'              => 'select',
    'source'             => 'flexibleblock/fblock_attribute_source_layout',
    'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
    'required'           => false,
    'sort_order'         => 16
));
$installer->addAttribute(MF_Flexibleblock_Model_Fblock::ENTITY, 'custom_layout_handle', array(
    'type'               => 'varchar',
    'label'              => 'Custom Layout Handle',
    'input'              => 'text',
    'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
    'required'           => false,
    'sort_order'         => 18
));
$installer->addAttribute(MF_Flexibleblock_Model_Fblock::ENTITY, 'additional_cache_tags', array(
    'type'               => 'varchar',
    'label'              => 'Additional Cache Tags',
    'input'              => 'text',
    'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
    'required'           => false,
    'sort_order'         => 45,
    'note'               => "separated by comma ','"
));
$installer->addAttribute(MF_Flexibleblock_Model_Fblock::ENTITY, 'layout_handle_2', array(
    'type'               => 'varchar',
    'label'              => 'Layout Handle 2',
    'input'              => 'select',
    'source'             => 'flexibleblock/fblock_attribute_source_layout',
    'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
    'required'           => false,
    'sort_order'         => 170
));
$installer->addAttribute(MF_Flexibleblock_Model_Fblock::ENTITY, 'position_2', array(
    'type'               => 'varchar',
    'label'              => 'Position 2',
    'input'              => 'select',
    'source'			 => 'flexibleblock/fblock_attribute_source_position',
    'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
    'required'           => false,
    'sort_order'         => 180
));
$installer->addAttribute(MF_Flexibleblock_Model_Fblock::ENTITY, 'layout_handle_3', array(
    'type'               => 'varchar',
    'label'              => 'Layout Handle 3',
    'input'              => 'select',
    'source'             => 'flexibleblock/fblock_attribute_source_layout',
    'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
    'required'           => false,
    'sort_order'         => 190
));
$installer->addAttribute(MF_Flexibleblock_Model_Fblock::ENTITY, 'position_3', array(
    'type'               => 'varchar',
    'label'              => 'Position 3',
    'input'              => 'select',
    'source'			 => 'flexibleblock/fblock_attribute_source_position',
    'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
    'required'           => false,
    'sort_order'         => 200
));
$installer->addAttribute(MF_Flexibleblock_Model_Fblock::ENTITY, 'layout_attribute', array(
    'type'               => 'varchar',
    'label'              => 'Layout Attribute 1',
    'input'              => 'text',
    'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
    'required'           => false,
    'sort_order'         => 210
));
$installer->addAttribute(MF_Flexibleblock_Model_Fblock::ENTITY, 'layout_attribute_2', array(
    'type'               => 'varchar',
    'label'              => 'Layout Attribute 2',
    'input'              => 'text',
    'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
    'required'           => false,
    'sort_order'         => 220
));
$installer->addAttribute(MF_Flexibleblock_Model_Fblock::ENTITY, 'layout_attribute_3', array(
    'type'               => 'varchar',
    'label'              => 'Layout Attribute 3',
    'input'              => 'text',
    'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
    'required'           => false,
    'sort_order'         => 230
));
$installer->addAttribute(MF_Flexibleblock_Model_Fblock::ENTITY, 'layout_attribute_custom', array(
    'type'               => 'varchar',
    'label'              => 'Layout Attribute Custom',
    'input'              => 'text',
    'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
    'required'           => false,
    'sort_order'         => 240
));
$installer->addAttribute(MF_Flexibleblock_Model_Fblock::ENTITY, 'display_pc', array(
    'type'               => 'int',
    'label'              => 'Display On PC',
    'input'              => 'select',
    'source'			 => 'eav/entity_attribute_source_boolean',
    'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
	'default'			 => 0,
    'required'           => false,
    'sort_order'         => 260
));
$installer->addAttribute(MF_Flexibleblock_Model_Fblock::ENTITY, 'display_tablet', array(
    'type'               => 'int',
    'label'              => 'Display On Tablet',
    'input'              => 'select',
    'source'			 => 'eav/entity_attribute_source_boolean',
    'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
	'default'			 => 0,
    'required'           => false,
    'sort_order'         => 280
));
$installer->addAttribute(MF_Flexibleblock_Model_Fblock::ENTITY, 'display_mobile', array(
    'type'               => 'int',
    'label'              => 'Display On Mobile',
    'input'              => 'select',
    'source'			 => 'eav/entity_attribute_source_boolean',
    'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
	'default'			 => 0,
    'required'           => false,
    'sort_order'         => 300
));
?>