<?php
class EM_Themeframework_Block_Adminhtml_Theme_Import_Grid_Tab_Slideshow extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('slideshowGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('ASC');
		$this->setFilterVisibility(false);	
		$this->setPagerVisibility(false);	
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		$this->setVarNameFilter('slideshow_import_grid');
	}


	protected function _prepareColumns()
	{
		$this->addColumn('in_block', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_block',
          //      'values'            => $this->_defaultCheckAll(),
                'align'             => 'center',
                'index'             => 'identifier',
				'use_index'			=>	true,
            ));
	
	$this->addColumn('name', array(
			'header'    => Mage::helper('themeframework')->__('Name'),
			'align'     =>'left',
			'index'     => 'name',
			'sortable'  => false,	
		));
		
		$this->addColumn('identifier', array(
			'header'    => Mage::helper('themeframework')->__('Identifier'),
			'align'     =>'left',
			'index'     => 'identifier',
			'sortable'  => false,	
		));
	  
      return parent::_prepareColumns();
  }



    public function _prepareCollection()
    {
    	$collection = new Varien_Data_Collection();//Mage::getResourceSingleton('themeframework/theme_list');
		$block_list = $this->getImportSlideshow();
		foreach($block_list->slideshow  as $key => $v){
			$data = new Varien_Object(); 
            $data->setData('block_id',$key);
            $data->addData(Mage::helper('themeframework/managetheme')->_xmlToArray($v));                      
            $collection->addItem($data);
        }
		$_mini_block_list = $this->getImportMiniSlideshow();
        foreach($_mini_block_list->minislideshow  as $_minikey => $_miniv){
			$_minidata = new Varien_Object(); 
            $_minidata->setData('block_id',$_minikey);
            $_minidata->addData(Mage::helper('themeframework/managetheme')->_xmlToArray($_miniv));                      
            $collection->addItem($_minidata);
        }
		
       $this->setCollection($collection);
		return parent::_prepareCollection();
    }

/*	public function _defaultCheckAll()
    {
    	$keyData = array();    	
		$page_list = $this->getData('data');		
		foreach($page_list->slideshow  as $key => $v){
			$data = array();             
            $data = Mage::helper('themeframework/managetheme')->_xmlToArray($v);                                  
            $keyData[] = $data['identifier'];
        }		        
		return $keyData;
    }*/
     public function _checkedSlideshow()
    {    	
        $list = $this->getImportSlideshow();  
		$keyData = array();
		if($list){
			foreach($list->slideshow  as $key => $v)
			{
				$data = array();             
				$data = Mage::helper('themeframework/managetheme')->_xmlToArray($v);                                  
				$keyData[] = $data['identifier'];
			}
		}
		
		$_minilist = $this->getImportMiniSlideshow();  
		if($_minilist){
			foreach($_minilist->minislideshow  as $_minikey => $_miniv)
			{
				$_minidata = array();             
				$_minidata = Mage::helper('themeframework/managetheme')->_xmlToArray($_miniv);                                  
				$keyData[] = $_minidata['identifier'];
			}
		}
		
		return $keyData;
    }
}