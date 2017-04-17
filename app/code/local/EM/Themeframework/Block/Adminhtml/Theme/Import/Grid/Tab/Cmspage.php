<?php
class EM_Themeframework_Block_Adminhtml_Theme_Import_Grid_Tab_Cmspage extends Mage_Adminhtml_Block_Widget_Grid
{
	

	public function __construct()
	{
		parent::__construct();
		$this->setId('cmspageGrid');
		$this->setFilterVisibility(false);	
		$this->setPagerVisibility(false);	
		$this->setSaveParametersInSession(true);
		$this->setVarNameFilter('cmspage_import_grid');
		$this->setUseAjax(true);
	}


	protected function _prepareColumns()
	{

		$this->addColumn('in_cmspage', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_cmspage',
               //'values'            =>  $this->_defaultCheckAll(),
                'align'             => 'center',
                'index'             => 'identifier',
				'use_index'			=>	true,								
            	'sortable'  => false,						
            ));
	

		$this->addColumn('title', array(
			'header'    => Mage::helper('themeframework')->__('Title'),
			'align'     =>'left',
			'index'     => 'title',			
            'sortable'  => false,						
		));
		
		$this->addColumn('identifier', array(
			'header'    => Mage::helper('themeframework')->__('URL Key'),
			'align'     =>'left',
			'index'     => 'identifier',
			'sortable'  => false,	
		));
	  
      return parent::_prepareColumns();
  }



    public function _prepareCollection()
    {
    	$collection = new Varien_Data_Collection();
		$page_list = $this->getImportPage();
		foreach($page_list->page  as $key => $v){
			$data = new Varien_Object(); 
            $data->setData('page_id',$key);
            $data->addData(Mage::helper('themeframework/managetheme')->_xmlToArray($v));                      
            $collection->addItem($data);            
            
        }		
        $this->setCollection($collection);
		return parent::_prepareCollection();
    }




    public function _defaultCheckAll()
    {
    	$keyData = array();    	
		$page_list = $this->getImportPage();		
		foreach($page_list->page  as $key => $v){
			$data = array();             
            $data = Mage::helper('themeframework/managetheme')->_xmlToArray($v);                                  
            $keyData[] = $data['identifier'];
        }		        
		return $keyData;
    }
	public function _checkedPage()
    {    	
        $page_list = $this->getImportPage();  
		$keyData = array();
		if($page_list)
			foreach($page_list->page  as $key => $v)
				{
					$data = array();             
		            $data = Mage::helper('themeframework/managetheme')->_xmlToArray($v);                                  
		            $keyData[] = $data['identifier'];
	        	}		

		return $keyData;
    }

}