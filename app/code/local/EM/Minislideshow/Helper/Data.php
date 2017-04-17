<?php
class EM_Minislideshow_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function emslider_encode($data){
		if(!$data) return "";
		else{
			return json_encode($data,JSON_HEX_TAG);
		}
	}
	
	public function emslider_decode($data){
		if(!$data) return "";
		else{
			return json_decode($data,true);
		}
	}
}