<?php
class MF_Flexibleblock_Model_Resource_Attribute_Collection extends Mage_Eav_Model_Resource_Entity_Attribute_Collection
{
	/**
     * Resource model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('flexibleblock/attribute', 'eav/entity_attribute');
    }

    /**
     * initialize select object
     *
     * @return MF_Flexibleblock_Model_Resource_Attribute_Collection
     */
    protected function _initSelect()
    {
        $entityTypeId = (int)Mage::getModel('eav/entity')->setType(MF_Flexibleblock_Model_Fblock::ENTITY)->getTypeId();
        $columns = $this->getConnection()->describeTable($this->getResource()->getMainTable());
        unset($columns['attribute_id']);
        $retColumns = array();
        foreach ($columns as $labelColumn => $columnData) {
            $retColumns[$labelColumn] = $labelColumn;
            if ($columnData['DATA_TYPE'] == Varien_Db_Ddl_Table::TYPE_TEXT) {
                $retColumns[$labelColumn] = Mage::getResourceHelper('core')->castField('main_table.'.$labelColumn);
            }
        }
        $this->getSelect()
            ->from(array('main_table' => $this->getResource()->getMainTable()), $retColumns)
            ->join(
                array('fblock_additional_table' => $this->getTable('flexibleblock/eav_attribute')),
                'fblock_additional_table.attribute_id = main_table.attribute_id'
            )
            ->where('main_table.entity_type_id = ?', $entityTypeId);
        return $this;
    }
}