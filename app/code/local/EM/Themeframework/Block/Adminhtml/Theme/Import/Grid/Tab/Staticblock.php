<?php
class EM_Themeframework_Block_Adminhtml_Theme_Import_Grid_Tab_Staticblock extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('blockGrid');
		$this->setFilterVisibility(false);
		$this->setPagerVisibility(false);		
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		$this->setVarNameFilter('staticblock_import_grid');
	}


	protected function _prepareColumns()
	{
		$this->addColumn('in_block', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_block',
            //    'values'            => $this->_defaultCheckAll(),                
                'align'             => 'center',
                'index'             => 'identifier',
				'use_index'			=>	true,
            ));
	

		$this->addColumn('title', array(
			'header'    => Mage::helper('themeframework')->__('Title'),
			'align'     =>'left',
			'index'     => 'title',
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
		$block_list = $this->getImportBlock();
		if($block_list)		
		{
			foreach($block_list->block  as $key => $v){
			    $data = new Varien_Object(); 
	            $data->setData('block_id',$key);
	            $data->addData(Mage::helper('themeframework/managetheme')->_xmlToArray($v));                      
	            $collection->addItem($data);
	        }
		}
		
       $this->setCollection($collection);
	return parent::_prepareCollection();
    }
   /* public function _defaultCheckAll()
    {
    	$keyData = array();    	
		$page_list = $this->getData('data');		
		foreach($page_list->block  as $key => $v){
			$data = array();             
            $data = Mage::helper('themeframework/managetheme')->_xmlToArray($v);                                  
            $keyData[] = $data['identifier'];
        }		        
		return $keyData;
    }*/

    public function _checkedBlock()
    {    	
        $list = $this->getImportBlock();  
		$keyData = array();
		if($list)
			foreach($list->block  as $key => $v)
				{
					$data = array();             
		            $data = Mage::helper('themeframework/managetheme')->_xmlToArray($v);                                  
		            $keyData[] = $data['identifier'];
	        	}		

		return $keyData;
    }
}