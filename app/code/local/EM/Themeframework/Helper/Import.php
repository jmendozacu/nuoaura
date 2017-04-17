<?php
class EM_Themeframework_Helper_Import extends Mage_Core_Helper_Abstract{
    
    protected $_blockIds = array();
    protected $_blockUpdateIds = array();
    protected $_pageUpdateIds = array();
    protected $_pageIds = array();    
    protected $_menuIds = array();
    protected $_slideshowIds = array();
    protected $_minislideshowIds = array();
    protected $_megaMenuModel = 'themeframework/megamenupro';
    protected $_themeFrameworkAreaModel = 'themeframework/area';
    protected $_storeId = 0;


    public function installSampleData($_themeSlug, $isFirst = false){

        $this->storeId = 0;
        $path = Mage::getBaseDir('design').DS.'frontend'.DS.str_replace('/',DS,$_themeSlug).DS.'etc'.DS.'init.xml';

        if (file_exists($path)) {
            $text = file_get_contents($path);
            $xmlData = simplexml_load_string($text);
            $xmlData = $xmlData->sample_data;
            //echo "<pre>";print_r($xmlData);
            if($isFirst){
                $this->importThemeFrameworkArea($xmlData);
               
                $this->importStaticContent($xmlData);
               
                $this->importMegaMenu($xmlData);
               
                $this->importSlideshow($xmlData);                
               
                $this->updateWidgetTabsId();                
               
                $this->importFlexibleBlock($xmlData);
               
            }            
        }
        
        return $this;
    
    }
    
    public function installPermissionBlock($_baseTheme, $_isFirst = false){
        $_path = Mage::getBaseDir('design').DS.'frontend'.DS.$_baseTheme.DS.'default'.DS.'etc'.DS.'permission_block.xml';
        if (file_exists($_path)) {
            $_text = file_get_contents($_path);            
            $_xmlData = simplexml_load_string($_text);            
            
            if($_isFirst){
                $_pblockNodes = $_xmlData->permission_block;
                
                if($_pblockNodes->pblock)
                {
                    foreach($_pblockNodes->pblock as $v){      
                        $_data = array();
                        $_data = Mage::helper('themeframework/managetheme')->_xmlToArray($v);
                        if(isset($v[0])){
                            $_model = Mage::getModel('admin/block')->getCollection()
                            ->addFieldToFilter('block_name', $_data['block_name'])->getFirstItem();
                            if(!$_model->getId()){                            
                                $_model->setData($_data);
                                $_model->save();
                                //echo 'aaaaaaaaaaaaaa';exit;
                            }                       
                        } 
                    }
                }
               
            }       
        } 
        return $this;
    }

    public function importSampleData($sample_data,$filterData,$store_id){        
        $this->_storeId = $store_id;        
        if(isset($filterData['importlayout']))
            $this->importThemeFrameworkArea($sample_data,$filterData['importlayout']);        
        $this->importStaticContent($sample_data,$filterData);
        if(isset($filterData['importmenu']))
            $this->importMegaMenu($sample_data,$filterData['importmenu']);
        if(isset($filterData['importslideshow']))
            $this->importSlideshow($sample_data,$filterData['importslideshow']);        

        $this->updateWidgetTabsId();                     
        if(isset($filterData['importfblock']))
            $this->importFlexibleBlock($sample_data,$filterData['importfblock']);        
        return $this;
    }
    
    /**
     * Import flexible block data
     */
    public function importFlexibleBlock($xmlData,$filterData = true){        
        $blockNodes = $xmlData->flexible_block;              
        $filterFblock = array();
        if(!is_bool($filterData))
            $filterFblock = explode('&', $filterData);
        if($blockNodes->fblock)
        {
            foreach($blockNodes->fblock as $v){            
                $data = Mage::helper('themeframework/managetheme')->_xmlToArray($v);  
                if(in_array($data['identifier'],$filterFblock) || ($filterData == true && is_bool($filterData)))              
                {
    				$fblock = Mage::getModel('flexibleblock/fblock')->getCollection()->setStoreId($this->_storeId)
                        ->addFieldToFilter('package_theme',$data['package_theme'])                
                        ->addFieldToFilter('identifier', $data['identifier'])->getFirstItem();
                    
                    if(preg_match('/{{widget type="tabs\/group"(.*)}}/U',$data['content'],$matches)){                
                        $data['content'] =  preg_replace_callback('/{{widget type="tabs\/group"(.*)}}/U', array($this,'replacer'), $data['content']);
                    }
                    if(count($this->_menuIds)>0)
                    {
                        if(preg_match('/{{widget type="megamenupro\/megamenupro"(.*)}}/U',$data['content'],$matches)){                
                            $data['content'] =  preg_replace_callback('/{{widget type="megamenupro\/megamenupro"(.*)}}/U', array($this,'replacerMenu'), $data['content']);
                        }
                    }
                    
                    if(count($this->_slideshowIds)>0)
                    {                        
                        if(preg_match('/{{widget type="slideshow2\/slideshow2"(.*)}}/U',$data['content'],$matches)){                
                            $data['content'] =  preg_replace_callback('/{{widget type="slideshow2\/slideshow2"(.*)}}/U', array($this,'replacerSlideshow'), $data['content']);
                        }
                    }
                    
                    if(count($this->_minislideshowIds)>0)
                    {                        
                        if(preg_match('/{{widget type="minislideshow\/minislideshow"(.*)}}/U',$data['content'],$matches)){                
                            $data['content'] =  preg_replace_callback('/{{widget type="minislideshow\/minislideshow"(.*)}}/U', array($this,'replacerMiniSlideshow'), $data['content']);
                        }
                    }

    				$fblock->setStoreId($this->_storeId);
                    if(count($this->_pageIds)>0)
                    {
                        if(isset($this->_pageIds[$data['cms_page']]))
                        {
                            $data['cms_page'] = $this->_pageIds[$data['cms_page']];
                        }
                            
                    }
                    if($fblock->getId())                            
                    {                    
                        $fblock->addData($data)->save(); 
                    }
                    else{
    					if($this->_storeId == 0){
    						$fblock->setStoreId(0)->setData($data)->save();
    					} else {
    						$data['status'] = 0;					
    						$fblock->setStoreId(0)->setData($data)->save();
    						$fblock->setStatus($data['status'])->setStoreId($this->_storeId);
    						$fblock->getResource()->saveAttribute($fblock,'status');
    					}
    					
    				}	
                }       
            }
        }
        return $this;
    }
    
    /**
        Import static block and static page from sampledata
    */
    public function importStaticContent($xmlDoc,$filterData = true){
        /* Import static block */
        $blockNodes = $xmlDoc->staticblock;
        $filterBlock = array();
	    if(isset($filterData['importblock']))
	        $filterBlock = explode('&', $filterData['importblock']);
        //echo '<pre>';print_r($blockNodes);exit;
        if($blockNodes->block)
        {
            foreach($blockNodes->block as $v){             

                $data = array();
                $data = Mage::helper('themeframework/managetheme')->_xmlToArray($v);
                if(in_array($data['identifier'],$filterBlock) || ($filterData == true && is_bool($filterData)))          
                {
                    $oldId = 0;
                    if(isset($v['id']))
                        $oldId = (string) $v['id'];            
                    if(isset($v[0]))                
                        $this->insertStaticBlock($data,$oldId);
                }
            }
        }
        $filterPage = array();
	    if(isset($filterData['importpage']))
        	$filterPage = explode('&', $filterData['importpage']);
        /* Import cms page */
        $pageNodes = $xmlDoc->cmspage;
        if($pageNodes->page)
        {
            foreach($pageNodes->page  as $v){ 
                $data = array();
                $data = Mage::helper('themeframework/managetheme')->_xmlToArray($v);            
                if(in_array($data['identifier'],$filterPage) || ($filterData == true && is_bool($filterData)))          
                {
                    $oldPageId = 0;
                    if(isset($v['id']))
                        $oldPageId = (string) $v['id'];            
                    if(isset($v[0]))                
                        $this->insertPage($data,$oldPageId);   
                }
            }
        }
        return $this;
    }
    
    public function insertStaticBlock($dataBlock,$id) {        
        // insert a block to db if not exists
        $block = Mage::getModel('cms/block')->getCollection()
                    ->addStoreFilter($this->_storeId, false)
                    ->addFieldToFilter('identifier', $dataBlock['identifier'])->getFirstItem();
        $oldId = $id;
        unset($dataBlock['id']);        
        if($block->getId())
        {            
            $block->addData($dataBlock);            
        }   
        else
            $block->setData($dataBlock);
        $block->setStores(array($this->_storeId))->save();
        $this->_blockIds[$oldId] = $block->getId();
        if(preg_match('/{{widget type="tabs\/group"(.*)}}/U',$dataBlock['content'],$matches))
            $this->_blockUpdateIds[$oldId] = $block;        
        return $block;
    }
    
    public function insertPage($dataPage,$oldPageId) {
        $page = Mage::getModel('cms/page')->getCollection()
                                ->addStoreFilter($this->_storeId, false)
                                ->addFieldToFilter('identifier', $dataPage['identifier'])->getFirstItem();
        if ($page->getId())
            $page->addData($dataPage)->setStores(array($this->_storeId))->save();             
        else
            $page->setData($dataPage)->setStores(array($this->_storeId))->save();
        $this->_pageIds[$oldPageId] = $page->getId();
        if(preg_match('/{{widget type="tabs\/group"(.*)}}/U',$dataPage['content'],$matches))
            $this->_pageUpdateIds[$oldPageId] = $page;
        return $page;
    }
    
    public function importMegaMenu($xmlDoc,$filterData= true){
        $menus = $xmlDoc->megamenu;
        $filterBlock = array();
        if(!is_bool($filterData))
            $filterBlock = explode('&', $filterData);
        if($menus->menu)
        {
            foreach($menus->menu as $menu){
                $data = Mage::helper('themeframework/managetheme')->_xmlToArray($menu);
                $oldMenuId = 0;
                if(isset($menu['id']))
                    $oldMenuId = (string) $menu['id'];                  
                if(in_array($data['identifier'],$filterBlock) || ($filterData == true && is_bool($filterData)))          
                {
                    if(Mage::getConfig()->getModuleConfig('EM_Megamenupro')->is('active', 'true'))
                    {
                        $model = Mage::getModel('megamenupro/megamenupro')->load($data['identifier'],'identifier');
                        if($model->getId())
                            $model->addData($data);
                        else
                            $model->setData($data);
                    }
                    else
                        $model = Mage::getModel('themeframework/megamenupro')->setData($data);                                                        
                    $model->save();
                    $this->_menuIds[$oldMenuId] = $model->getId();
                }
            }
        }
        return $this;
    }
    
    public function importSlideshow($xmlDoc,$filterData= true){
        $templateFile1 = Mage::getBaseDir('etc') . DS . 'modules' . DS . 'EM_Slideshow2.xml';
        if (file_exists($templateFile1)) {
            $slideshowsNode = $xmlDoc->slideshows; 
            $filterBlock = array(); 
            if(!is_bool($filterData))      
                $filterBlock = explode('&', $filterData); 
            if($slideshowsNode->slideshow)               
                {foreach($slideshowsNode->slideshow as $slideshow){
                    $data = Mage::helper('themeframework/managetheme')->_xmlToArray($slideshow);   
    
                    if(in_array($data['identifier'],$filterBlock) || ($filterData == true && is_bool($filterData)))          
                    {                            
                        $oldSlideshowId = 0;
                        if(isset($slideshow['id']))
                            $oldSlideshowId = (string) $slideshow['id'];
                        $model = Mage::getModel('slideshow2/slider')->load($data['identifier'],'identifier');                    
                        if($model->getId())
                            $model->addData($data);
                        else
                            $model->setData($data);
                        //$model->setData($data)->setCreatedTime(now())->setUpdateTime(now())->save();
                        $model->save();
                        $this->_slideshowIds[$oldSlideshowId] = $model->getId();
                    }
                }
            }
        }
        
        $templateFile2 = Mage::getBaseDir('etc') . DS . 'modules' . DS . 'EM_Minislideshow.xml';
        if (file_exists($templateFile2)) {
            $_minislideshowsNode = $xmlDoc->minislideshows; 
            $_minifilterBlock = array(); 
            if(!is_bool($filterData))      
                $_minifilterBlock = explode('&', $filterData); 
            if($_minislideshowsNode->minislideshow)               
                {foreach($_minislideshowsNode->minislideshow as $_minislideshow){
                    $_minidata = Mage::helper('themeframework/managetheme')->_xmlToArray($_minislideshow);   
    
                    if(in_array($_minidata['identifier'],$_minifilterBlock) || ($filterData == true && is_bool($filterData)))          
                    {                            
                        $_minioldSlideshowId = 0;
                        if(isset($_minislideshow['id']))
                            $_minioldSlideshowId = (string) $_minislideshow['id'];
                        $_minimodel = Mage::getModel('minislideshow/slider')->load($_minidata['identifier'],'identifier');                    
                        if($_minimodel->getId())
                            $_minimodel->addData($_minidata);
                        else
                            $_minimodel->setData($_minidata);
                        //$model->setData($data)->setCreatedTime(now())->setUpdateTime(now())->save();
                        $_minimodel->save();
                        $this->_minislideshowIds[$_minioldSlideshowId] = $_minimodel->getId();
                    }
                }
            }
        }        
         
        return $this;
    }       
    
    public function importThemeFrameworkArea($xmlDoc,$filterData= true){
        $areas = $xmlDoc->themeframework;
        $filterBlock = array();
        if(!is_bool($filterData))
            $filterBlock = explode('&', $filterData);        
        if($areas->area)
        {
            $i = 0;
            foreach($areas->area as $area){
                $i++;
                $data = Mage::helper('themeframework/managetheme')->_xmlToArray($area);  
                if(in_array($i,$filterBlock)|| ($filterData == true && is_bool($filterData)))          
                {  
                    //echo "<pre>";print_r($data);exit;
                    $model = Mage::getModel($this->_themeFrameworkAreaModel)->getCollection()->addStoreFilter($this->_storeId, false)
                        ->addFieldToFilter('package_theme',$data['package_theme'])
                        ->addFieldToFilter('layout',$data['layout'])
                        ->getFirstItem();           
                    
                    $data['content_decode'] = unserialize($data['content']); 
                    unset($data['content']);
                    
                    if($model->getId())
                        $model->addData($data)->setStores(array($this->_storeId));
                    else
                        $model->setData($data)->setStores(array($this->_storeId));
                    
                    $model->save();
                }

            }
        }
        return $this;
    }
    
    public function replacer($m){
        $seps = explode(' ',$m[1]);        
        $paramsString = '{{widget type="tabs/group"';
        foreach($seps as $sep){
            if(preg_match('/block_(.*)"(.*)"/U',$sep)){
                $sep = preg_replace_callback('/(.*)"(.*)"/U',array($this,'replacement'),$sep);
            }
            $paramsString .= $sep.' ';
        }        
        return trim($paramsString,' ').'}}';
    }

    public function replacement($matches){        
        return $matches[1].'"'.$this->_blockIds[$matches[2]].'"';
    }
    
    public function updateWidgetTabsId(){
        foreach($this->_blockUpdateIds as $block){
            $content = preg_replace_callback('/{{widget type="tabs\/group"(.*)}}/U', array($this,'replacer'), $block->getContent());
            $block->setData('content',$content)->save();;
        }
        foreach($this->_pageUpdateIds as $page){
            $content = preg_replace_callback('/{{widget type="tabs\/group"(.*)}}/U', array($this,'replacer'), $page->getContent());
            $page->setData('content',$content)->save();;
        }
        return $this;
    }

    public function replacerMenu($m){
        $seps = explode(' ',$m[1]);        
        $paramsString = '{{widget type="megamenupro/megamenupro"';
        foreach($seps as $sep){
            if(preg_match('/menu="(.*)"/U',$sep)){                
                $sep = preg_replace_callback('/"(.*)"/U',array($this,'replacementMenu'),$sep);
            }
            $paramsString .= $sep.' ';
        }        
        return trim($paramsString,' ').'}}';
    }

    public function replacementMenu($matches){        
        return '"'.$this->_menuIds[$matches[1]].'"';
    }

    public function replacerMiniSlideshow($m){
        $_miniseps = explode(' ',$m[1]);        
        $_miniparamsString = '{{widget type="minislideshow/minislideshow"';
        foreach($_miniseps as $_minisep){
            if(preg_match('/slideshow="(.*)"/U',$_minisep)){                
                $_minisep = preg_replace_callback('/"(.*)"/U',array($this,'replacementMiniSlideshow'),$_minisep);
            }
            $_miniparamsString .= $_minisep.' ';
        }        
        return trim($_miniparamsString,' ').'}}';
    }
    
    public function replacerSlideshow($m){
        $seps = explode(' ',$m[1]);        
        $paramsString = '{{widget type="slideshow2/slideshow2"';
        foreach($seps as $sep){
            if(preg_match('/slideshow="(.*)"/U',$sep)){                
                $sep = preg_replace_callback('/"(.*)"/U',array($this,'replacementSlideshow'),$sep);
            }
            $paramsString .= $sep.' ';
        }        
        return trim($paramsString,' ').'}}';
    }

    public function replacementSlideshow($matches){        
        return '"'.$this->_slideshowIds[$matches[1]].'"';
    }
    
    public function replacementMiniSlideshow($matches){        
        return '"'.$this->_minislideshowIds[$matches[1]].'"';
    }    

    public function importSettings($import,$filter,$themeId)
    {                  
        $theme = Mage::getModel('themeframework/theme')->load($themeId);
        $import = Mage::helper('themeframework/managetheme')->_xmlToArray($import);
        $conftheme = (array)json_decode($theme->getConfigJson());        
        if('typographysetting' == $filter)
        {
            $prefixArray = array('css','fonts','typo_general','header','menu','body','footer','buttons'); 
            $conftheme = (array)json_decode($theme->getConfigJson());
            $confimport = (array)json_decode($import['settings']['config_json']);
            
            foreach ($confimport as $key => $value) {
                foreach ($prefixArray as $prefix) {
                    if($this->startsWith($key,$prefix))
                    {
                       $conftheme[$key] =  $value;
                       break;
                    }
                }
            }
            $import['settings']['config_json'] = json_encode($conftheme);
        }
        /*if(isset($import['package']))
            $theme->setPackage($import['package']);
        if(isset($import['template']))
            $theme->setTemplage($import['template']);
        if(isset($import['layout']))
            $theme->setLayout($import['layout']);
        if(isset($import['skin']))
            $theme->setSkin($import['skin']);*/
        if($import && $theme)
        {
            if('excluded_blocks' == $filter)                    
                $theme->setExcludedBlocks($import['settings']['excluded_blocks']);        
            else
                $theme->setConfigJson($import['settings']['config_json']);             
            $theme->save();          
        }
        
    }
    function startsWith($haystack, $needle) {    
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

}