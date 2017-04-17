<?php
class EM_Megamenupro_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();
		$this->renderLayout();
    }

	public function updateAction()
    {
		$collection = Mage::getModel('megamenupro/megamenupro')->getCollection();
		foreach($collection as $value){
			$str	=	unserialize($value->getContent());
			if($str == ""){
				echo "\"".$value->getName()."\" menu has been converted <br />";
			}else{
				$value->setContent(Zend_Json::encode($str));
				$value->save();
				echo "\"".$value->getName()."\" menu has been convert completed <br />";
			}
		}
		echo 'waiting.......<br />';
		Mage::helper("megamenupro")->getFlushCache('all');
		echo 'Successful';exit;

    }
    
    public function reloadAction()
    {
        $id = $this->getRequest()->getParam('id');
        $block = $this->getLayout()->createBlock('megamenupro/megamenupro');
	    $block->setData('menu',$id);
	    $block->setData('cache',0);
	    $data = $block->getMegamenupro();
	    die('ab');
    }
}
