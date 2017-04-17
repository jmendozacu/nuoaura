<?php
class EM_Minislideshow_Block_Minislideshow extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

	public function _toHtml(){
		$this->setTemplate('em_minislideshow/minislideshow.phtml');
		return parent::_toHtml();
	}

	public function getSlider()
    {
		$id	=	$this->getData('slideshow');
		$helper = Mage::helper("minislideshow");
		$slider  = Mage::getModel('minislideshow/slider')->load($id)->getData();
		$slider['slider_params']	=	$helper->emslider_decode($slider['slider_params']);
		$slider['appearance']		=	$helper->emslider_decode($slider['appearance']);
		$slider['navigation']		=	$helper->emslider_decode($slider['navigation']);

		return $slider;
    }

	public function getImages($images)
    {
		$images	=	Mage::helper("minislideshow")->emslider_decode($images);
		
		return $images;
	}

	public function getResponsitiveValues($params){
		$sliderWidth = (int)$params['size_width'];
		$sliderHeight = (int)$params['size_height'];
		
		//add main item:
		$arr["sliderWidth"] = $sliderWidth;
		$arr["sliderHeight"] = $sliderHeight;
		
		return($arr);
	}
	
	public function getResizeImage($name,$width = 255, $height = 255){
		if(!$name) return;

		$imagePathFull = Mage::getBaseDir('media').DS.'em_minislideshow'.DS.$name;
		$resizePath = $width . 'x' . $height;
		$resizePathFull = Mage::getBaseDir('media'). DS .'em_minislideshow' . DS . 'resize' . DS . $resizePath . DS . $name;

		if (file_exists($imagePathFull) && !file_exists($resizePathFull)) {
			$imageObj = new Varien_Image($imagePathFull);
			$imageObj->constrainOnly(TRUE);
			$imageObj->resize($width,$height);
			$imageObj->save($resizePathFull);
		}

		return Mage::getBaseUrl('media'). 'em_minislideshow/resize/' . $resizePath . "/"  . $name;	
	}
}