<?php
class EM_Megamenupro_Block_Megamenupro extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
	protected $time_cache = 60;
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

    public function getMegamenuproBK()
    {
        $id     = $this->getData('menu');
		if($id){
			$lib_multicache	=	Mage::helper('megamenupro/multicache');
			$storeId = Mage::app()->getStore()->getId();
			$cache	 = Mage::helper("megamenupro")->getEnableCache();
			$data	 = "";
			if($cache == 1)
				$data	=	$lib_multicache->get('megamenupro_'.$storeId.'_full_'.$id);

			if(!$data){
				$data  = Mage::getModel('megamenupro/megamenupro')->load($id)->getData();
				
				if($data && $cache == 1){
					$this->time_cache	=	Mage::helper("megamenupro")->getCacheTime(60);
					$lib_multicache->set('megamenupro_'.$storeId.'_full_'.$id,$data,$this->time_cache*60);
				}
			}
		}
		if ($data['status']==0)	// check menu is disabled
			return array('type' => 1, 'content' => ''); 
		$menu['type']		=	$data['type'];
		$content	=	Zend_Json::decode($data['content']);
		$helper = Mage::helper('cms');
		$processor = $helper->getPageTemplateProcessor();
		if(is_array($content)){
			foreach($content as $k=>$v){
				if($v['type']	== 'text'){
					$tmp	=	"";
					$tmp	=	base64_decode($v['text']);
					$content[$k]['text'] = $processor->filter($tmp);
				}
			}
		}
		$menu['content']	=	$content;
		$menu['css_class'] = $data['css_class'];
		return $menu;
    }
    
    public function getMegamenupro()
    {
        $id     = $this->getData('menu');
        $cache  = $this->getData('cache') === null ? 1 : $this->getData('cache');
        $menu = '';
        $storeId = Mage::app()->getStore()->getId();
        
        $cacheId = 'megamenupro_'.$storeId.'_full_'.$id;
        
        //$cache = 0;
        $menu = Mage::app()->getCache()->load($cacheId);
        
        if ($cache == 1 && $menu) 
        {
            $menu = Mage::app()->getCache()->load($cacheId);
            $menu = unserialize($menu);
        } else {
            $menu = '';
            if($id){
			    $data  = Mage::getModel('megamenupro/megamenupro')->load($id)->getData();
		    }
		    if ($data['status']==0)	// check menu is disabled
			    return array('type' => 1, 'content' => ''); 
		    $menu['type']		=	$data['type'];
		    $content	=	Zend_Json::decode($data['content']);
		    $helper = Mage::helper('cms');
		    $processor = $helper->getPageTemplateProcessor();
		    if(is_array($content)){
			    foreach($content as $k=>$v){
				    if($v['type']	== 'text'){
					    $tmp	=	"";
					    $tmp	=	base64_decode($v['text']);
					    $content[$k]['text'] = $processor->filter($tmp);
				    }
			    }
		    }
		    $menu['content']	=	$content;
		    $menu['css_class'] = $data['css_class'];
		    //Mage::app()->getCache()->clean('all',array($cacheId));
            Mage::app()->getCache()->save(serialize($menu), $cacheId,array(), 360000);
        }

		return $menu;
    }

	public function _toHtml(){
		$data = $this->getMegamenupro();

		$this->assign('data',$data);
		$this->assign('menu',$data['content']);

		$this->setTemplate('em_megamenupro/showmenu.phtml');
		return parent::_toHtml();
	}

	public function close_tags(&$close_tags, $item_depth) {
		ksort($close_tags);
		$close_tags = array_reverse($close_tags, true);
		$html ="";
		foreach ($close_tags as $depth => $tag) {
			if ($item_depth <= $depth) {
				$html .= $tag."<!-- $depth -->\n";
				unset($close_tags[$depth]);
			}

			if ($item_depth < $depth)
				$html .= "</ul><!-- $depth -->\n";
				
			if ($item_depth > $depth)
				break;
		}
		return $html;
	}

	public function open_tag($close_tags, $item_depth, $container_css = '') {
		$html = "";
		if (!empty($close_tags) && max(array_keys($close_tags)) < $item_depth)
			$html .= '<ul class="menu-container"' . ($container_css ? " style=\"$container_css\"" : '') . '>';
		return $html;
	}

	public function getIconImage($url){
		$model	=	Mage::getModel("core/email_template_filter");
		$image_url = $model->filter($url);
		return $image_url;
	}
}
