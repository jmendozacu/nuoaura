<?php
class EM_Themeframework_Block_Adminhtml_Theme_Import_Grid_Tab_Layout extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('layoutGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('ASC');
		$this->setFilterVisibility(false);	
		$this->setPagerVisibility(false);	
		$this->setSaveParametersInSession(true);
		$this->setVarNameFilter('layout_import_grid');
		$this->setUseAjax(true);
	}


	protected function _prepareColumns()
	{
		$this->addColumn('in_layout', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_layout',
             //  'values'            => $this->_defaultCheckAll(),
                'align'             => 'center',
                'index'             => 'layout_id',
				'use_index'			=>	true,				
            ));
	
		$this->addColumn('layout_id', array(
			'header'    => Mage::helper('themeframework')->__('ID'),
			'align'     =>'left',
			'index'     => 'layout_id',
			'sortable'  => false,	
		));
		$this->addColumn('package_theme', array(
			'header'    => Mage::helper('themeframework')->__('Package/Theme'),
			'align'     =>'left',
			'index'     => 'package_theme',
			'sortable'  => false,	
		));
		$this->addColumn('name', array(
			'header'    => Mage::helper('themeframework')->__('Name'),
			'align'     =>'left',
			'index'     => 'name',
			'sortable'  => false,	
		));
		$this->addColumn('layout', array(
			'header'    => Mage::helper('themeframework')->__('Layout'),
			'align'     =>'left',
			'index'     => 'layout',
			'sortable'  => false,	
		));
	  
      return parent::_prepareColumns();
  }



    public function _prepareCollection()
    {
    	$collection = new Varien_Data_Collection();//Mage::getResourceSingleton('themeframework/theme_list');
		$block_list = $this->getImportLayout();
		$i = 1;
		foreach($block_list->area  as $key => $v){
			$data = new Varien_Object(); 
            $data->setData('layout_id',$i);
            $data->addData(Mage::helper('themeframework/managetheme')->_xmlToArray($v));                      
            $collection->addItem($data);
            $i++;
        }
		
       $this->setCollection($collection);
		return parent::_prepareCollection();
    }

  /*	public function _defaultCheckAll()
    {
    	$keyData = array();    	
		$page_list = $this->getData('data');	
		$i = 1;	
		foreach($page_list->area  as $key => $v){
			$data = new Varien_Object(); 
            $data->setData('layout_id',$i);
            $data->addData(Mage::helper('themeframework/managetheme')->_xmlToArray($v));   
            $keyData[] = $data['layout_id'];
            $i++;
        }		        
		return $keyData;
    }*/
    public function _checkedLayout()
    {    	
        $list = $this->getImportLayout();  
		$keyData = array();
		if($list)
			foreach($list->area  as $key => $v)
				{
					$data = array();             
		            $data = Mage::helper('themeframework/managetheme')->_xmlToArray($v);                                  
		            $keyData[] = $data['identifier'];
	        	}		

		return $keyData;
    }

}