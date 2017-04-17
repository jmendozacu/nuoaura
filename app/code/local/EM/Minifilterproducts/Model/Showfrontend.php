<?php
class EM_Minifilterproducts_Model_Showfrontend extends Mage_Core_Model_Abstract
{
    /**
     * @return array
     */
    public function toOptionArray()
	{
		$result[] = array('value' => 'thumb',		'label' =>  'Thumbnail');
		$result[] = array('value' => 'name',		'label' =>  'Name');
		$result[] = array('value' => 'sku',			'label' =>  'SKU');
		$result[] = array('value' => 'desc',		'label' =>  'Description');
		$result[] = array('value' => 'review',		'label' =>  'Review');
		$result[] = array('value' => 'price',		'label' =>  'Price');
		$result[] = array('value' => 'label',		'label' =>  'Label');
		$result[] = array('value' => 'addtocart',	'label' =>  'Add to Cart');
		$result[] = array('value' => 'addto',		'label' =>  'Wishlist - Compare');
		return $result;
	}
}
?>