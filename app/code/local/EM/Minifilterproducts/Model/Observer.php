<?php
class EM_Minifilterproducts_Model_Observer
{
    public function cleanBestSellerCache($observer)
	{
		Mage::app()->cleanCache(array(EM_Minifilterproducts_Block_List::BEST_SELLER_CACHE_TAG));
	}
}
?>
