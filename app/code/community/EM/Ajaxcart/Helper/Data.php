<?php
class EM_Ajaxcart_Helper_Data extends Mage_Core_Helper_Abstract
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
			$value = Mage::getStoreConfig('em_ajaxcart/'.implode('/', $segs));
			if (!$value) $value = @$args[0];
			return $value;
		}
		else 
			call_user_func_array(array($this, $name), $args);
	}

    public function getOptionsHtml($controller,$productId=null)
    {
		$specifyOptions = $controller->getRequest()->getParam('options');

        // Prepare helper and params
        $viewHelper = Mage::helper('ajaxcart/product_view');

        $params = new Varien_Object();
        //$params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);

        try {
			$viewHelper->prepareAndRender($productId, $controller, $params);
        } catch (Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
            } else {
                Mage::logException($e);
            }
        }
    }

	public function checkMobilePhp() {
		require_once(Mage::getBaseDir('lib') . DS . 'em/Mobile_Detect.php');
		$check = 0;
		$detect = new Mobile_Detect();
        if($detect->isMobile() && !$detect->isTablet()){
			$check = 1;
		}
		return $check;
	}
}