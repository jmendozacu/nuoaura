<?php

class EM_Quickshop_Helper_Data extends Mage_Core_Helper_Abstract
{
	function getConfig($key, $section = 'general') {
		return Mage::getStoreConfig("quickshop/$section/$key");
	}
}