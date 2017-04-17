<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<?php
class EM_Themeframework_Model_Observer extends Varien_Object
{
	protected $_pages = null;
	protected function initPages($handles){
		if(!$this->_pages){
			$pages = Mage::getSingleton('themeframework/page')->getCollection()->addFieldToFilter('status',1)
				->addStoreFilter(Mage::app()->getStore()->getId());

			// add or condition for handle & custom_handle attribute
			foreach($handles as &$handle)
				$handle = '\'' . $handle . '\'';
			$cond = implode(",", $handles);
			$where = $pages->getSelect()->getPart(Zend_Db_Select::WHERE);
			$where[] = " AND (handle IN ($cond) OR custom_handle IN ($cond))";
			$pages->getSelect()->setPart(Zend_Db_Select::WHERE, $where);
			
			$pages->getSelect()->order('sort DESC');	
			$this->_pages = $pages;	
		}
		return $this->_pages;	
	}
	
	/* Update template */
    public function changeTemplateEvent($observer) {
		$handles = $observer->getEvent()->getLayout()->getUpdate()->getHandles();
		$pages = $this->initPages($handles);
		if(!$pages->count())
			return $this;
		
		$layout = '';
		
		foreach($pages as $page){
			if((in_array($page->getHandle(),$handles) || $page->getHandle() == 'custom_handle') && ($page->getLayout()))
				$layout = $page->getLayout();
		}
		$observer->getEvent()->getAction()->getLayout()->helper('page/layout')->applyTemplate($layout);
    }

	/* Update custom layout */
	public function changeLayoutEvent(Varien_Event_Observer $observer){
		/* Register theme */
		$this->registerTheme();
		
		$handles = $observer->getEvent()->getLayout()->getUpdate()->getHandles();
        /* Change position of default blocks */
        $this->changePositionBlocks($observer);
		$pages = $this->initPages($handles);
		if(!$pages->count())
			return $this;
		$update = $observer->getEvent()->getLayout()->getUpdate();
		foreach($pages as $page){
			if(in_array($page->getHandle(),$handles) || $page->getHandle() == 'custom_handle'){
				$layoutUpdate = $page->getLayoutUpdateXml();
				if(!empty($layoutUpdate)){
					$update->addUpdate($layoutUpdate);
				}	
			}			
		}
		return $this;
	}	
	
	public function processAfterHtmlDispatch($observer) {
		
		$cookie = Mage::getSingleton('core/cookie');
		$key = $cookie->get('EDIT_BLOCK_KEY');
		if (!$key) return;
		
		$block = $observer->getEvent()->getData('block');
		$name = $block->getNameInLayout();
		
		// is static block
		if (is_a($block, 'Mage_Cms_Block_Block') || is_a($block, 'Mage_Cms_Block_Widget_Block') || is_a($block,'MF_Flexibleblock_Block_Fblock')) {
			if(is_a($block,'MF_Flexibleblock_Block_Fblock')){
				$model = $block->getBlock();
				$title = $model->getTitle();
				$url = Mage::helper('adminhtml')->getUrl('adminhtml/fblock/edit',array('id' => $model->getId(),'key' => $cookie->get('EDIT_FBLOCK_KEY')));
			} else {
				$block_id = $block->getBlockId();
				$model = Mage::getModel('cms/block')
					->setStoreId(Mage::app()->getStore()->getId())
					->load($block_id);
				if (!($id = $model->getId())) $id = $block_id;
				
				$title = $model->getTitle();
				$url = Mage::helper('adminhtml')->getUrl("adminhtml/cms_block/edit", array('block_id' => $id, 'key' => $key));
			}
			$transport = $observer->getEvent()->getTransport();
			
			$html = trim($transport->getHtml());
			$transport->setHtml($html
				."<div class=\"em_themeframework_previewblock".(!$html ? ' empty' : '')."\" style=\"display:none\">"
				."<a target=\"_blank\" href=\"$url\">$title</a>"
				."</div>");
		} 
		// is widget
		elseif (strlen($name) == 32 && preg_replace('/[^a-z0-9]/', '', $name) == $name) {
			$transport = $observer->getEvent()->getTransport();
			$html = trim($transport->getHtml());
			$transport->setHtml($html
				."<div class=\"em_themeframework_previewblock".(!$html ? ' empty' : '')."\" style=\"display:none\">"
				."Widget ".$block->getType()
				."<br/><span class=\"path\">".$block->getTemplateFile()."</span>"
				."</div>");

		}
	}
	
	/**
     * Add typography wysiwyg plugin config
     *
     * @param Varien_Event_Observer $observer
     * @return EM_Themeframework_Model_Observer
     */
    public function prepareWysiwygPluginConfig(Varien_Event_Observer $observer)
    {
        $config = $observer->getEvent()->getConfig();
        $settings = Mage::getModel('themeframework/typography_config')->getWysiwygPluginSettings($config);
        $config->addData($settings);
        return $this;
    }
	
	public function registerTheme() {
		/* Register current theme */
		if(Mage::registry('register_theme'))
			return $this;
		Mage::register('register_theme',1);	
        $currentStore = Mage::app()->getStore()->getStoreId();

        $designChange = Mage::getSingleton('core/design')
            ->loadChange($currentStore);

        if ($designChange->getData()) {
            $design = Mage::getModel('themeframework/design')->load($designChange->getDesignChangeId(),'design_change_id');
            $theme = Mage::getModel('themeframework/theme')->load($design->getThemeId());
            $theme->addJsonConfigData();
            Mage::register('em_current_theme',$theme);
            return $this;
        }

        $package = Mage::getSingleton('core/design_package')->getPackageName();
        
        $default = EM_Themeframework_Model_Theme::DEFAULT_THEME_NAME;        
        $default_theme = Mage::getSingleton('core/design_package')->getTheme('default') == $default ? '' : Mage::getSingleton('core/design_package')->getTheme('default');
        
        $skin = Mage::getSingleton('core/design_package')->getTheme('skin') == $default ? '' : Mage::getSingleton('core/design_package')->getTheme('skin');
        $layout = Mage::getSingleton('core/design_package')->getTheme('layout') == $default ? '' : Mage::getSingleton('core/design_package')->getTheme('layout');
        $template = Mage::getSingleton('core/design_package')->getTheme('template') == $default ? '' : Mage::getSingleton('core/design_package')->getTheme('template');

        $idTheme = Mage::getStoreConfig(EM_Themeframework_Model_Theme::CONFIG_PATH_ACTIVE,Mage::app()->getStore()->getId());
        
		if($idTheme && $idTheme != 'NULL'){
			$theme = Mage::getModel('themeframework/theme')->load($idTheme);
			if(($package == $theme->getPackage()) &&
				($skin == $theme->getSkin()) && 
				($layout == $theme->getLayout()) && 
				($template == $theme->getTemplate()) &&
				($default_theme == $theme->getDefaultTheme())
			){                

				$theme->addJsonConfigData();            
				Mage::register('em_current_theme',$theme);
				return $this;
			}
		}
		$collection = Mage::getResourceModel('themeframework/theme_collection')
			->addFieldToSelect('*')
			->addFieldToFilter('package',$package)
			->addFieldToFilter('skin',$skin)
			->addFieldToFilter('layout',$layout)
			->addFieldToFilter('template',$template)
			->addFieldToFilter('default_theme',$default_theme)
			->addFieldToFilter('is_clone','0');
		if($collection->getSize() > 0){
			$theme = $collection->getFirstItem();
			$theme->addJsonConfigData();
			Mage::register('em_current_theme',$theme);
		}
		return $this;
	}
	
	public function reactiveTheme(Varien_Event_Observer $observer) {
		$data = Mage::getSingleton('adminhtml/config_data')->getData();
        $isChanged = isset($data['groups']['package']['fields']['name']['value']) || isset($data['groups']['theme']['fields']['template']['value']) ||
            isset($data['groups']['theme']['fields']['skin']['value']) || isset($data['groups']['theme']['fields']['layout']['value']) ||
            isset($data['groups']['theme']['fields']['default']['value']);

        if($isChanged){
            if($data['scope'] == 'stores'){
                $configObject = Mage::app()->getStore($observer->getEvent()->getStore());
            } elseif($data['scope'] == 'websites'  || $data['scope'] == 'default'){
                $configObject = Mage::app()->getWebsite($observer->getEvent()->getWebsite());
            }
            //echo '<pre>';print_r($configObject->getData());exit;
            $package = $configObject->getConfig('design/package/name');
            $template = $configObject->getConfig('design/theme/template');
            $layout = $configObject->getConfig('design/theme/layout');
            $skin = $configObject->getConfig('design/theme/skin');
            $default = $configObject->getConfig('design/theme/default');            
            //echo $package.'<br/>'.$template.'<br/>'.$layout.'<br/>'.$skin.'<br/>'.$default.'<br/>';exit;
            $collection = Mage::getModel('themeframework/theme')->getCollection()
                ->addFieldToFilter('package',$package)
                ->addFieldToFilter('layout',$layout)
                ->addFieldToFilter('template',$template)
                ->addFieldToFilter('skin',$skin)
                ->addFieldToFilter('default_theme',$default)
                ->setOrder('theme_id','DESC');
            //echo $data['scope'].' '.$data['scope_id'];exit;
            if($collection->getSize() > 0){
                $theme = $collection->getFirstItem();
                //echo '<pre>';print_r($theme->getData());exit;
                $theme->setPathActive($data['scope'],$data['scope_id']);
                $theme->importSampleData();
            } else {
                //echo $data['scope_id'];exit;
                Mage::getModel('core/config')->saveConfig(EM_Themeframework_Model_Theme::CONFIG_PATH_ACTIVE,'NULL',$data['scope'],$data['scope_id']);
                //Mage::getModel('themeframework/theme')->unsetPathActive($data['scope'],$data['scope_id']);
            }
        } else {
            Mage::getModel('core/config')->deleteConfig(EM_Themeframework_Model_Theme::CONFIG_PATH_ACTIVE,$data['scope'],$data['scope_id']);
        }
	}

    public function reactiveByDesign(Varien_Event_Observer $observer){
        $object = $observer->getEvent()->getObject();
        if($object instanceof Mage_Core_Model_Design){
            list($package,$identitifer) = explode('/',$object->getDesign());
            $collection = Mage::getModel('themeframework/theme')->getCollection()
                ->addFieldToFilter('package',$package)
                ->addFieldToFilter('identifier',$identitifer);

            if($collection->getSize() > 0){
                $theme = $collection->getFirstItem();
                $theme->importSampleData();
                if($object->getId()){
                    $design = Mage::getModel('themeframework/design')->load($object->getId(),'design_change_id');
                } else {
                    $design = Mage::getModel('themeframework/design');
                }

                $design->addData(array('design_change_id' => $object->getId(),'theme_id' => $theme->getId()))->save();
            }
        }
    }
	
	public function getExcludedFBlock($observer)
	{
        $packageTheme = array();        
        $theme = Mage::registry('em_current_theme');

		$object = $observer->getEvent()->getObject();
		if($theme){
			if($theme->getExcludedBlocks() != '')
			{
				
                $exclBlocks = explode('&', $theme->getExcludedBlocks());
                $object->setExcludeBlocks($exclBlocks);
            }	
			$packageTheme = $observer->getEvent()->getPackageTheme();
			if($theme->getIdentifier() != $theme->getBaseTheme()){
                $packageTheme[0] = $theme->getPackage().'/'.$theme->getTemplate();
				$packageTheme[1] = $theme->getPackage().'/'.($theme->getDefaultTheme() ? $theme->getDefaultTheme(): EM_Themeframework_Model_Theme::DEFAULT_THEME_NAME);              
				$object->setPackageTheme($packageTheme);
			}
            
            
		}        
		return $this;
	}

    public function changePositionBlocks($observer){
        if($theme = Mage::registry('em_current_theme')){
            $update = $observer->getEvent()->getLayout()->getUpdate();
            $handles = $update->getHandles();
            $helper = Mage::helper('themeframework/managetheme');
            $default_theme  =  $theme->getDefaultTheme() ? $theme->getDefaultTheme() : EM_Themeframework_Model_Theme::DEFAULT_THEME_NAME;
            $prefixTagXml = $theme->getPackage().'/';

            /* Load reset xml */
            //echo Mage::getSingleton('core/design_package')->getBaseDir().'etc'.DS.'reset.php';exit;
            $xmlRestArray = require_once(Mage::getSingleton('core/design_package')->getBaseDir(array('_type' => 'etc','_package' => $theme->getPackage(),'_theme' => $default_theme)).DS.'reset.php');
            $xmlUpdateString = '';
            if(count($xmlRestArray)){
                foreach($xmlRestArray as $keyHandle => $value){
                    if(in_array($keyHandle,$handles))
                        $xmlUpdateString .= $value;
                }
            }
            $update->addUpdate($xmlUpdateString);
            $prefixDataXml = $prefixTagXml.'sidebar/block_data/';
            //echo '<pre>';print_r($update->getHandles());exit;
            $blocks = array('left' => array('blocks' => array(),'unset'=>array()),'right' => array('blocks' => array(),'unset'=>array()));

            $list = explode(',',Mage::getStoreConfig($prefixTagXml.'sidebar/list'));

            /* Build array blocks */
            foreach($list as $l){
                $position = $helper->getConfigTheme('position_'.$l);
                if($position == 'none' ||
                    (($handle = Mage::getStoreConfig($prefixDataXml.$l.'/handles')) && (count(array_intersect(explode(',',$handle),$handles)) == 0)))
                    continue;

                $type = Mage::getStoreConfig($prefixDataXml.$l.'/type');
                $template = Mage::getStoreConfig($prefixDataXml.$l.'/template');
                $name = Mage::getStoreConfig($prefixDataXml.$l.'/name');
                $as = Mage::getStoreConfig($prefixDataXml.$l.'/as');
                if(($type == 'layerednavigation/catalog_layer') && (!Mage::getConfig()->getModuleConfig('EM_LayeredNavigation')->is('active', 'true'))){
                    $type = 'catalog/layer_view';
                    $template = 'catalog/layer/view.phtml';
                    $name = 'catalog.leftnav.custom';
                }

                if(($type == 'layerednavigation/search_layer') && (!Mage::getConfig()->getModuleConfig('EM_LayeredNavigation')->is('active', 'true'))){
                    $type = 'catalogsearch/layer';
                    $template = 'catalog/layer/view.phtml';
                    $name = 'catalogsearch.leftnav.custom';
                }

                $stringXml = '<block type="'.$type.'" name="'.$name.'"';
                if($as)
                    $stringXml .= ' as="'.$as.'"';
                //echo Mage::getStoreConfig($prefixTagXml.$l.'/type');exit;
                if($template)
                    $stringXml .= ' template="'.$template.'"';
                if($before = Mage::getStoreConfig($prefixDataXml.$l.'/before'))
                    $stringXml .= ' before="'.$before.'"';
                if($after = Mage::getStoreConfig($prefixDataXml.$l.'/after'))
                    $stringXml .= ' after="'.$after.'"';
                if($action = Mage::getStoreConfig($prefixDataXml.$l.'/action')){
                    /* Build action method */
                    $stringXml .= '>';
                    $actionArray = explode('|',$action);
                    foreach($actionArray as $act){
                        list($methodString,$paramString) = explode('?',$act);
                        $methodData = explode(',',$methodString);
                        $stringXml .= '<action';
                        foreach($methodData as $method){
                            list($label,$value) = explode(':',$method);
                            $stringXml .= ' '.preg_replace('~[\r\n\t]+~', '', $label).'="'.preg_replace('~[\r\n\t]+~', '', str_replace('"',',',$value)).'"';
                            //$stringXml .= '<'.$values[0].'>'.$values[1].'</'.$values[0].'>';
                        }
                        $stringXml .= '>';
                        $paramsData = explode(',',$paramString);
                        foreach($paramsData as $param){
                            list($label,$value) = explode(':',$param);
                            $stringXml .= '<'.preg_replace('~[\r\n\t]+~', '', $label).'>'.preg_replace('~[\r\n\t]+~', '', $value).'</'.preg_replace('~[\r\n\t]+~', '', $label).'>';
                        }
                        $stringXml .= '</action>';
                    }
                    $stringXml .= '</block>';
                } else{
                    $stringXml .= '/>';
                }
                if($position == 'all'){
                    $blocks['left']['blocks'][] = $stringXml;
                    $blocks['right']['blocks'][] = $stringXml;
                } else {
                    $blocks[$position]['blocks'][] = $stringXml;
                }

            }
            /* Build xml update */
            $xml = '';
            // Reference name="left"
            foreach($blocks as $position => $listXml){
                if((count($listXml['blocks']) > 0) || (count($listXml['unset']) > 0)){
                    $xml .= '<reference name="'.$position.'">';

                    /* unset block */
                    /*if(count($listXml['unset']) > 0){
                        foreach($listXml['unset'] as $name){
                            $xml .= '<action method="unsetChild"><name>'.$name.'</name></action>';
                        }
                    }*/

                    /* Add block */
                    if(count($listXml['blocks']) > 0){
                        foreach($listXml['blocks'] as $block){
                            $xml .= $block;
                        }
                    }
                    $xml .= '</reference>';
                }

            }
            $update->addUpdate($xml);
        }
        return $this;
    }


    public function compressHtml($observer) {
        
        if (Mage::helper('themeframework/settings')->enableCompressHTML()):
            // Fetches the current event
            $event = $observer->getEvent();  
            $controller = $event->getControllerAction();
            $allHtml = $controller->getResponse()->getBody();
            // Trim each line
            $allHtml = preg_replace('/^\\s+|\\s+$/m', '', $allHtml);
            // Remove HTML comments
            $allHtml =  preg_replace_callback(
                '/<!--([\\s\\S]*?)-->/',
                array($this, '_commentCB'),
                $allHtml); 
            // Remove ws around block/undisplayed elements
            $allHtml = preg_replace('/\\s+(<\\/?(?:area|base(?:font)?|blockquote|body'
                .'|caption|center|cite|col(?:group)?|dd|dir|div|dl|dt|fieldset|form'
                .'|frame(?:set)?|h[1-6]|head|hr|html|legend|li|link|map|menu|meta'
                .'|ol|opt(?:group|ion)|p|param|t(?:able|body|head|d|h||r|foot|itle)'
                .'|ul)\\b[^>]*>)/i', '$1', $allHtml);
            // Remove ws outside of all elements
            $allHtml = preg_replace_callback(
                '/>([^<]+)</',
                array($this, '_outsideTagCB'),
                $allHtml);
            $controller->getResponse()->setBody($allHtml);
        endif;
        }

    protected function _outsideTagCB($m)
    {
        if (Mage::helper('themeframework/settings')->enableCompressHTML()):
            return '>' . preg_replace('/^\\s+|\\s+$/', ' ', $m[1]) . '<';
        endif;
    }

    protected function _commentCB($m)
    {
        if (Mage::helper('themeframework/settings')->enableCompressHTML()):
            return (0 === strpos($m[1], '[') || false !== strpos($m[1], '<!['))
                ? $m[0]
                : '';
        endif;
    }
}
?>
