<?php
class EM_Minifilterproducts_Model_Orderby extends Mage_Core_Model_Abstract 
{
    /**
     * @return array
     */
    public function toOptionArray()
	{
		$result[] = array('value' => 'name asc','label' =>  'Name ASC');
		$result[] = array('value' => 'name desc','label' =>  'Name DESC');
		$result[] = array('value' => 'price asc','label' =>  'Price ASC');
		$result[] = array('value' => 'price desc','label' =>  'Price DESC');
        $result[] = array('value' => 'entity_id asc','label' =>  "Product's ID ASC");        
        $result[] = array('value' => 'entity_id desc','label' =>  "Product's ID DESC");
        $result[] = array('value' => 'em_order_random','label' =>  'Random');        
		return $result;
	}
}
?>
