<?php
class EM_Minifilterproducts_Model_Type extends Mage_Core_Model_Abstract 
{
    /**
     * @return array
     */
    public function toOptionArray()
	{
		$result[] = array('value' => 1,'label' =>  'Special Attribute');
		$result[] = array('value' => 2,'label' =>  'Bestseller Products');
		$result[] = array('value' => 3,'label' =>  'New Products');
		$result[] = array('value' => 4,'label' =>  'Sales Products');
		$result[] = array('value' => 5,'label' =>  'Normal Products');
        $result[] = array('value' => 6,'label' =>  'Most Viewed Products');
		return $result;
	}
}
?>