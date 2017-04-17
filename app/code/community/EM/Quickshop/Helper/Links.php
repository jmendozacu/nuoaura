<?php

class EM_Quickshop_Helper_Links extends Mage_Core_Helper_Abstract
{
	public function addQuickShopLink($productUrl){
		$_mypath = 'quickshop/index/view/path/';
		$_baseUrl = str_replace("index.php/","",Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)).'index.php/'.$_mypath;
		
		$_reloadUrl = $this->filterLink($productUrl);				
		$_reloadUrl = $_baseUrl . $_reloadUrl;
		return $_reloadUrl;
	}
    
    public function filterLink($link){

		$result = str_replace(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB),'',$link);
		$result = str_replace('index.php/', '', $result);
		$patn = "/catalog\\/product\\/view\\/id\\/(.*?)\\//i";		
		if (preg_match($patn, $result)) {
			preg_match($patn,$result,$s);			
            if(is_array($s)){
                $result = 'catalog/product/view/id/' . $s[1];
            }else{
                $result = preg_replace("/\\//i", "/",$result);
            }
		} else {
            $result = preg_replace("/\\//i", "_!_",$result);
		}		
		return $result;
	
	}
}
