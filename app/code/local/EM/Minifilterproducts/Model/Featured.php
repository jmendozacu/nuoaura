<?php
class EM_Minifilterproducts_Model_Featured extends Mage_Core_Model_Abstract 
{
    /**
     * @return array
     */
    public function toOptionArray()
	{
		$result[] = array('value' => 'em_featured','label' =>  'Featured Product');
		$result[] = array('value' => 'em_deal','label' =>  'Special Deal');
		$result[] = array('value' => 'em_hot','label' =>  'Hot Product');
		return $result;
	}
}
?>
