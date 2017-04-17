<?php
class EM_Megamenupro_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function __call($name, $args) {
		if (method_exists($this, $name))
			call_user_func_array(array($this, $name), $args);
		elseif (preg_match('/^get([^_][a-zA-Z0-9_]+)$/', $name, $m)) {
			$segs = explode('_', $m[1]);
			foreach ($segs as $i => $seg){
				//$segs[$i] = strtolower(preg_replace('/([^A-Z])([A-Z])/', '$1_$2', $seg));
				$seg = preg_replace('/([^A-Z])([A-Z])/', '$1_$2', $seg);
				$seg = preg_replace('/([A-Z])([A-Z])/', '$1_$2', $seg);
				$segs[$i] = strtolower(preg_replace('/([A-Z])([A-Z])/', '$1_$2', $seg));
			}
			$value = Mage::getStoreConfig('megamenupro/'.implode('/', $segs));
			if (!$value) $value = @$args[0];
			return $value;
		}
		else 
			call_user_func_array(array($this, $name), $args);
	}

	public function getFlushCache($name='all'){
		if($name == 'all'){
			$dir	=	Mage::getBaseDir('var').DS.'cache'.DS.'megamenupro';
			$this->delTree($dir);
		}else{
			foreach (Mage::app()->getWebsites() as $website) {
				foreach ($website->getGroups() as $group) {
					$stores = $group->getStores();
					foreach ($stores as $store) {
						$dir	=	Mage::getBaseDir('var').DS.'cache'.DS.'megamenupro'.DS.$store->getStoreId().DS.'full'.DS.'megamenupro_'.$store->getStoreId().'_full_'.$name.'.php';

						if(is_file($dir))	unlink($dir);
					}
				}
			}
		}
		return true;
    }

	protected function delTree($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}

	public function emmenu_encode($data){
		if(!$data) return "";
		else{
			return json_encode($data,JSON_HEX_TAG);
		}
	}

	public function emmenu_decode($data){
		if(!$data) return "";
		else{
			return json_decode($data,true);
		}
	}

	public function getNameImage($str){
		if(!$str) return;

		$model	=	Mage::getModel("core/email_template_filter");
		$plit 	= explode("/",$model->filter($str));
		$count 	= count($plit);
		$name 	= $plit[$count-1];

		return $name;
	}

	public function getResizeImage($name,$width = 55, $height = 55){
		if(!$name) return;

		$imagePathFull = Mage::getBaseDir('media') . DS . 'em' . DS . 'megamenupro' . DS . 'icon'. DS .$name;
		$resizePath = $width . 'x' . $height;
		$resizePathFull = Mage::getBaseDir('media') . DS . 'em' . DS . 'megamenupro' . DS . 'resize'. DS . $resizePath . DS . $name;

		if (file_exists($imagePathFull) && !file_exists($resizePathFull)) {
			$imageObj = new Varien_Image($imagePathFull);
			$imageObj->constrainOnly(TRUE);
			$imageObj->resize($width,$height);
			$imageObj->save($resizePathFull);
		}

		return Mage::getBaseUrl('media'). 'em/megamenupro/resize/' . $resizePath . "/"  . $name;	
	}
}