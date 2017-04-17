<?php
/**
 * @methods:
 * - get[Section]_[ConfigName]($defaultValue = '')
 */
class EM_Themeframework_Helper_Settings extends Mage_Core_Helper_Abstract
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
                        	
			$value = Mage::helper('themeframework/managetheme')->getConfigTheme(implode('_', $segs));
            
            /* Check if database have not saved yet */
            if($value == null || $value == ''){
                $_loadVal = $segs[0].'_'.$segs[1]; 
                if(Mage::registry('em_current_theme')!=NULL){
                    $_themeHelper = Mage::helper('themeframework/managetheme'); 
                    $_xmlValue = $_themeHelper->getDefaultXmlValueVariation($_loadVal);                                
                    $value = $_xmlValue;
                }
                /* if value field not exist */                
                if($value == null || $value == ''){   
                    $value = @$args[0];
                }
                //$value = @$args[0];
            }		
			/* if (!$value) $value = @$args[0]; */
			return $value;
		}
		
		else 
			call_user_func_array(array($this, $name), $args);
	}
    
	public function insertStaticBlock($dataBlock) {
		// insert a block to db if not exists
		$block = Mage::getModel('cms/block')->getCollection()->addFieldToFilter('identifier', $dataBlock['identifier'])->getFirstItem();
		if (!$block->getId())
			$block->setData($dataBlock)->save();
		return $block;
	}
	
	public function insertPage($dataPage) {
		$page = Mage::getModel('cms/page')->getCollection()->addFieldToFilter('identifier', $dataPage['identifier'])->getFirstItem();
		if (!$page->getId())
			$page->setData($dataPage)->save();
		return $page;
	}
	
    public function checkMobilePhp() {
		if(!class_exists('Mobile_Detect')){
			require_once(Mage::getBaseDir('lib') . DS . 'em/Mobile_Detect.php');
		}
		$detect = new Mobile_Detect();
        $checkmobile = $detect->isMobile();
        $checktablet = $detect->isTablet();        
        if($checkmobile){
            if($checktablet){
                return false;
            }else{
                return true;
            }
            
        }else{
            return false;
        }
	}
	public function isShowOfferPrice($productPrice){
		if(!Mage::registry('current_product'))
			return false;
		return Mage::registry('current_product')->getId() == $productPrice->getId();
	}
    
    public function checkMobile() {
		require_once(Mage::getBaseDir('lib') . DS . 'em/Mobile_Detect.php');
		$detect = new Mobile_Detect();
        $checkmobile = $detect->isMobile();
        if($checkmobile){
            return 'true';            
        }else{
            return 'false';
        }
	}
    
    public function checkTabletPhp() {
		require_once(Mage::getBaseDir('lib') . DS . 'em/Mobile_Detect.php');
		$detect = new Mobile_Detect();
        $checktablet = $detect->isTablet();
        if($checktablet){
            return 'true';
        }else{
            return 'false';
        }
	}
	
	public function getActionReview(){
		$url = Mage::helper('core/url')->getCurrentUrl();
		$url_check = 'wishlist/index/configure';
		$url_check2 = 'checkout/cart/configure';
		if(stripos($url,$url_check)){
			$id = Mage::registry('current_product')->getId();
			return Mage::getUrl('review/product/post/', array('id' => $id,'_secure' => true));
		} else {
			if(stripos($url,$url_check2)){
				$id = Mage::getSingleton('catalog/session')->getLastViewedProductId();
				return Mage::getUrl('review/product/post/', array('id' => $id,'_secure' => true));
			}else{
				$productId = Mage::app()->getRequest()->getParam('id', false);
				return Mage::getUrl('review/product/post', array('id' => $productId,'_secure' => true));
			}
		}
	}
    
    /**
     *  multi deal
     **/
    public function getPercentOff($_product) {
		$specialPrice = $_product->getSpecialPrice();
		$regularPrice = $_product->getPrice();
		if($specialPrice > 0 && $regularPrice != 0){
			$off	=	 number_format(100*(float)($regularPrice-$specialPrice)/$regularPrice,0);
			$html	=	"<span class='sale_off'>off <span>".$off.$this->__("%")."</span></span>";
			return $html;
		}
		else
			return 0;
	}
    
    public function getCategoriesCustom($parent,$curId){
				
		try{
			$children = $parent->getChildrenCategories();
						
		}
		catch(Exception $e){
			return '';
		}
		return $children;
	}
    
    public function checkWindowsMobileOS() {
		require_once(Mage::getBaseDir('lib') . DS . 'em/Mobile_Detect.php');
		$detect = new Mobile_Detect();
        $checkWP = $detect->isWindowsMobileOS();
        if($checkWP){
            return true;
        }else{
            return false;
        }
	}
    
    public function checkDevice(){
        require_once(Mage::getBaseDir('lib') . DS . 'em/Mobile_Detect.php');
        $detect = new Mobile_Detect;
        if( $detect->isMobile() ){            
            if( $detect->isTablet() ){
                // Any tablet device.
                return 'tablet';
            }else{
                // Exclude tablets.
                return 'mobile';
            }
        }else{
            return 'desktop';
        }
    }

    public function enableCompressHTML(){
        return Mage::getStoreConfig("themeframework/general/enable_compress_html");
    }
    
    public function resizeMediaImage($srcUrl, $targetUrl, $fileName, $width = null, $height = null)
    {        
        $dirImgpath = Mage::getBaseDir('media') . DS . $srcUrl . DS . $fileName;
        $checkPath = preg_replace('/\\\\/', '/', $targetUrl);  
        $resizePath = $width . 'x' . $height;  
        $imageresized = Mage::getBaseDir('media') . DS . $targetUrl . DS . $resizePath . DS . $fileName;
        
        if (!file_exists($imageresized) && file_exists($dirImgpath)) {
            $dirUrl = ".media/$checkPath/$resizePath";
            if (!file_exists("$dirUrl"))
                mkdir("$dirUrl", 0777);

            $imageObj = new Varien_Image($dirImgpath);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(false);
            $imageObj->resize($width, $height);
            $imageObj->save($imageresized);
        }
        
        $resizeImageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $checkPath . '/' . $resizePath . '/' . $fileName;
        return $resizeImageUrl;
    }
  
}
