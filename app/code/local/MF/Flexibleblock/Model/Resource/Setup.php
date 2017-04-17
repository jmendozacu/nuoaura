<?php
class MF_Flexibleblock_Model_Resource_Setup extends Mage_Eav_Model_Entity_Setup
{
    /**
     * Prepare catalog attribute values to save
     *
     * @param array $attr
     * @return array
     */
    protected function _prepareValues($attr)
    {
        $data = parent::_prepareValues($attr);
        $data = array_merge($data, array(
            'position'                      => $this->_getValue($attr, 'sort_order', 0),
        ));
        return $data;
    }

     /**
     * Retreive default entities: post
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        $entities = array(
            MF_Flexibleblock_Model_Fblock::ENTITY    => array(
                'entity_model'            => 'flexibleblock/fblock',
				'attribute_model'		  => 'flexibleblock/attribute',
                'table'                          => 'flexibleblock/fblock',
				'additional_attribute_table'     => 'flexibleblock/eav_attribute',
				'entity_attribute_collection'    => 'flexibleblock/attribute_collection',
				'default_group'                  => 'General Information',
                'attributes'                     => array(
                    'title'          => array(
                        'type'               => 'varchar',
                        'label'              => 'Title',
                        'input'              => 'text',
                        'required'           => true,
						'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
                        'sort_order'         => 10
                    ),
					'position'=> array(
                        'type'               => 'varchar',
                        'label'              => 'Position 1',
                        'input'              => 'select',
						'source'			 => 'flexibleblock/fblock_attribute_source_position',
                        'required'           => false,
						'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
                        'sort_order'         => 20
                    ),
					'custom_position'=> array(
                        'type'               => 'varchar',
                        'label'              => 'Custom Position',
                        'input'              => 'text',
                        'required'           => false,
						'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
                        'sort_order'         => 30
                    ),
                    'cms_page'   => array(
                        'type'               => 'int',
                        'label'              => 'Show in cms page',
                        'input'              => 'select',
                        'source'             => 'flexibleblock/fblock_attribute_source_cmspage',
                        'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
                        'required'           => false,
                        'sort_order'         => 35
                    ),
                    'order'=> array(
                        'type'               => 'int',
                        'label'              => 'Sort order',
                        'required'           => false,
                        'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
                        'sort_order'         => 40
                    ),
					'status'         => array(
                        'type'               => 'int',
                        'label'              => 'Is Active',
                        'input'              => 'select',
						'source'             => 'eav/entity_attribute_source_boolean',
						'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
                        'required'           => true,
                        'sort_order'         => 50
                    ),
                    /*'sort_order'         => array(
                        'type'               => 'int',
                        'label'              => 'Sort Order',
                        'input'              => 'text',
						'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
                        'required'           => false,
                        'sort_order'         => 60
                    ),*/
					'content'         => array(
                        'type'               => 'text',
                        'label'              => 'Content',
                        'input'              => 'editor',
						'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
                        'required'           => true,
                        'sort_order'         => 70
                    ),
					'from_date'    => array(
                        'type'               => 'datetime',
                        'label'              => 'From Date',
                        'input'              => 'date',
                        'frontend'           => 'eav/entity_attribute_frontend_datetime',
                        'backend'            => 'eav/entity_attribute_backend_datetime',
                        'required'           => false,
						'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
                        'sort_order'         => 80,
                        'input_filter'       => 'date',
                        'validate_rules'     => 'a:1:{s:16:"input_validation";s:4:"date";}'
                    ),
					'to_date'    => array(
                        'type'               => 'datetime',
                        'label'              => 'To Date',
                        'input'              => 'date',
                        'frontend'           => 'eav/entity_attribute_frontend_datetime',
                        'backend'            => 'eav/entity_attribute_backend_datetime',
                        'required'           => false,
						'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
                        'sort_order'         => 90,
                        'input_filter'       => 'date',
                        'validate_rules'     => 'a:1:{s:16:"input_validation";s:4:"date";}'
                    ),
					'schedule_pattern'   => array(
                        'type'               => 'varchar',
                        'label'              => 'Schedule Pattern',
                        'input'              => 'select',
						'source'             => 'flexibleblock/fblock_attribute_source_pattern',
						'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
                        'required'           => false,
                        'sort_order'         => 100
                    ),
                    'schedule_from_time'=> array(
                        'type'               => 'varchar',
                        'label'              => 'From Time',
                        'input'              => 'text',
						'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
                        'required'           => false,
                        'sort_order'         => 110,
                        'note'               => 'hours:minutes:seconds (Ex : 20:05:30)'
                    ),
                    'schedule_to_time' => array(
                        'type'               => 'varchar',
                        'label'              => 'Date Time',
                        'input'              => 'text',
						'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
                        'required'           => false,
                        'sort_order'         => 120,
                        'note'               => 'hours:minutes:seconds (Ex : 20:05:30)'
                    ),
                    'created_at'         => array(
                        'type'               => 'static',
                        'input'              => 'text',
                        'backend'            => 'eav/entity_attribute_backend_time_created',
                        'sort_order'         => 130,
                        'visible'            => false,
                    ),
                    'updated_at'         => array(
                        'type'               => 'static',
                        'input'              => 'text',
                        'backend'            => 'eav/entity_attribute_backend_time_updated',
                        'sort_order'         => 140,
                        'visible'            => false,
                    ),
                    'category_ids' => array(
                        'type'               => 'varchar',
                        'label'              => 'Category Ids',
                        'input'              => 'text',
                        'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
                        'required'           => false,
                        'sort_order'         => 150
                    ),
                    'conditions'         => array(
                        'type'               => 'text',
                        'label'              => 'Conditions',
                        'input'              => 'textarea',
                        'global'             => MF_Flexibleblock_Model_Attribute::SCOPE_STORE,
                        'required'           => false,
                        'sort_order'         => 160,
                        'visible'            => false
                    )
                )
            )
        );
        return $entities;
    }
}
