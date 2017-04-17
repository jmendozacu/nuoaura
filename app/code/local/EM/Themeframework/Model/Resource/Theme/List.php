<?php 
class EM_Themeframework_Model_Resource_Theme_List extends Varien_Data_Collection
{
	const SORT_ORDER_ASC    = 'ASC';
    const SORT_ORDER_DESC   = 'DESC';
	
	
	/**
     * Implementation of IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
		if(!$this->getPageSize() || !$this->getCurPage())
			return parent::getIterator();
        $this->load();
		$start = $this->getPageSize()*($this->getCurPage() - 1);
        return new ArrayIterator(array_slice($this->_items, $start,$this->getPageSize()));
    }
}
?>