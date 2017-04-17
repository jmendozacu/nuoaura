<?php
class MF_Flexibleblock_Block_Adminhtml_Fblock_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('fblockGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$storeId = $this->getRequest()->getParam('store');
		$collection = Mage::getModel('flexibleblock/fblock')->getCollection()->setStoreId($storeId)
			->addAttributeToSelect('*');
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
	}

	protected function _prepareColumns()
	{
		$this->addColumn('entity_id', array(
			'header'    => Mage::helper('flexibleblock')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'entity_id',
		));

		$this->addColumn('title', array(
			'header'    => Mage::helper('flexibleblock')->__('Title'),
			'align'     =>'left',
			'index'     => 'title',
		));
		
		$this->addColumn('identifier', array(
			'header'    => Mage::helper('flexibleblock')->__('Identifier'),
			'align'     =>'left',
			'index'     => 'identifier',
		));
      
		$this->addColumn('package_theme', array(
            'header'    => Mage::helper('widget')->__('Design Package/Theme'),
            'align'     => 'left',
            'index'     => 'package_theme',
            'type'      => 'theme',
            'with_empty' => true,
        ));
	  
		$this->addColumn('position', array(
			'header'    => Mage::helper('flexibleblock')->__('Position 1'),
			'align'     =>'left',
			'index'     => 'position',
		));

        $this->addColumn('position_2', array(
            'header'    => Mage::helper('flexibleblock')->__('Position 2'),
            'align'     =>'left',
            'index'     => 'position_2',
        ));

        $this->addColumn('position_3', array(
            'header'    => Mage::helper('flexibleblock')->__('Position 3'),
            'align'     =>'left',
            'index'     => 'position_3',
        ));

		$this->addColumn('from_date', array(
			'header'    => Mage::helper('flexibleblock')->__('From Date'),
			'align'     =>'left',
			'index'     =>'from_date',
			'type'      =>'date',    
		));
		
		$this->addColumn('to_date', array(
			'header'    => Mage::helper('flexibleblock')->__('To Date'),
			'align'     =>'left',
			'index'     =>'to_date',
			'type'      =>'date',    
		));
      
		$this->addColumn('status', array(
			'header'    => Mage::helper('flexibleblock')->__('Status'),
			'align'     =>'left',
			'type'      =>'options',  
			'index'     =>'status',
			'options'   =>array(1 => Mage::helper('flexibleblock')->__('Enable'),0 => Mage::helper('flexibleblock')->__('Disable'))  
		));
       $this->addExportType('*/*/exportCsv', Mage::helper('flexibleblock')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('flexibleblock')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('fblock');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('flexibleblock')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('flexibleblock')->__('Are you sure?')
        ));

        //$statuses = Mage::getSingleton('blog/status')->getOptionArray();
        //$statuses = array(0=>'disable',1=>'Enable');
        //array_unshift($statuses, array('label'=>'', 'value'=>''));
        $statuses = array(
                        array('label'=>'Disabled', 'value'=>'0'),
                        array('label'=>'Enable', 'value'=>'1'),
        );  
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('flexibleblock')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('flexibleblock')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
  	
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}