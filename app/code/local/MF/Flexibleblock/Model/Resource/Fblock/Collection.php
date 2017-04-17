<?php
class MF_Flexibleblock_Model_Resource_Fblock_Collection extends MF_Flexibleblock_Model_Resource_Collection_Abstract
{
    /**
     * Product limitation filters
     * Allowed filters
     *  store_id                int;
     *  category_id             int;
     *  category_is_anchor      int;
     *  store_table             string;
     *
     * @var array
     */
    protected $_productLimitationFilters     = array();
	/**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('flexibleblock/fblock');
    }

    /**
     * Add store availability filter. Include availability product
     * for store website
     *
     * @param mixed $store
     * @return MF_Flexibleblock_Model_Resource_Fblock_Collection
     */
    public function addStoreFilter($store = null)
    {
        if ($store === null) {
            $store = $this->getStoreId();
        }
        $store = Mage::app()->getStore($store);
        if (!$store->isAdmin()) {
            $this->setStoreId($store);
            $this->_productLimitationFilters['store_id'] = $store->getId();
            $this->_applyProductLimitations();
        }

        return $this;
    }

    /**
     * Apply limitation filters to collection
     * for different combinations of store_id states
     * Method supports multiple changes in one collection object for this parameters
     *
     * @return MF_Flexibleblock_Model_Resource_Fblock_Collection
     */
    protected function _applyProductLimitations()
    {
        $this->_prepareProductLimitationFilters();
        return $this;
    }

    /**
     * Prepare limitation filters
     *
     * @return MF_Flexibleblock_Model_Resource_Fblock_Collection
     */
    protected function _prepareProductLimitationFilters()
    {
        if (!isset($this->_productLimitationFilters['store_id'])
        ) {
            $this->_productLimitationFilters['store_id'] = $this->getStoreId();
        }

        return $this;
    }
}
?>