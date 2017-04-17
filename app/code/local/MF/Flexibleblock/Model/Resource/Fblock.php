<?php
class MF_Flexibleblock_Model_Resource_Fblock extends MF_Flexibleblock_Model_Resource_Abstract
{
	/**
     * Resource initialization
     */
    public function __construct()
    {
        $this->setType('flexibleblock_fblock');
        $this->setConnection('flexibleblock_read', 'flexibleblock_write');
    }

    /**
     * Process fblock data before save
     *
     * @param Varien_Object $object
     * @return MF_Flexibleblock_Model_Resource_Fblock
     */
    protected function _beforeSave(Varien_Object $object)
    {
        /**
         * Check if declared category ids in object data.
         */
        if ($object->hasCategoryIds() && is_array($object->getCategoryIds()) && count($object->getCategoryIds())) {
            $categoryIds = Mage::getResourceSingleton('catalog/category')->verifyIds(
                $object->getCategoryIds()
            );
            $object->setData('category_ids',implode(',',$categoryIds));
        }
//echo '<pre>';print_r($object->getData());exit;
        return parent::_beforeSave($object);
    }
}
?>