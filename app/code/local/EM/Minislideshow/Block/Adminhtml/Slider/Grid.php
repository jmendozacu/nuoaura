<?php

class EM_Minislideshow_Block_Adminhtml_Slider_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('sliderGrid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection(){
		$collection = Mage::getModel('minislideshow/slider')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){
		$this->addColumn('id', array(
			'header'    => Mage::helper('minislideshow')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'id',
		));

		$this->addColumn('name', array(
			'header'    => Mage::helper('minislideshow')->__('Title'),
			'align'     =>'left',
			'width'     => '200px',
			'index'     => 'name',
		));

		$this->addColumn('identifier', array(
			'header'    => Mage::helper('minislideshow')->__('Identifier'),
			'align'     => 'left',
			'width'     => '200px',
			'index'     => 'identifier'
		));
		
		$this->addColumn('creation_time', array(
            'header'    => Mage::helper('minislideshow')->__('Date Created'),
            'index'     => 'creation_time',
			'width'     => '160px',
            'type'      => 'datetime',
        ));

		$this->addColumn('status', array(
			'header'    => Mage::helper('minislideshow')->__('Status'),
			'align'     => 'left',
			'width'     => '80px',
			'index'     => 'status',
			'type'      => 'options',
			'options'   => array(
				1 => 'Enabled',
				2 => 'Disabled',
			),
		));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction(){
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('minislideshow');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'    => Mage::helper('minislideshow')->__('Delete'),
			'url'      => $this->getUrl('*/*/massDelete'),
			'confirm'  => Mage::helper('minislideshow')->__('Are you sure?')
		));

		$statuses = Mage::getSingleton('minislideshow/status')->getOptionArray();

		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('minislideshow')->__('Change status'),
			'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name' => 'status',
					'type' => 'select',
					'class' => 'required-entry',
					'label' => Mage::helper('minislideshow')->__('Status'),
					'values' => $statuses
				)
			)
		));
		return $this;
	}

	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}