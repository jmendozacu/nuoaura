<?php
class EM_Minislideshow_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/minislideshow?id=15 
    	 *  or
    	 * http://site.com/minislideshow/id/15 	
    	 */
    	/* 
		$minislideshow_id = $this->getRequest()->getParam('id');

  		if($minislideshow_id != null && $minislideshow_id != '')	{
			$minislideshow = Mage::getModel('minislideshow/slider')->load($minislideshow_id)->getData();
		} else {
			$minislideshow = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($minislideshow == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$minislideshowTable = $resource->getTableName('minislideshow');
			
			$select = $read->select()
			   ->from($minislideshowTable,array('minislideshow_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$minislideshow = $read->fetchRow($select);
		}
		Mage::register('minislideshow', $minislideshow);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}