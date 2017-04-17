<?php
class EM_Megamenupro_Model_Update extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('megamenupro/update');
    }
	
	public function version($ver="")
    {
		if($ver == "1.0.0")	$this->version_100();

        return true;
    }

	protected function version_100()
    {
		$helper = Mage::helper("megamenupro");
		$collection =  Mage::getModel('megamenupro/megamenupro')->getCollection();
		

		if($collection->getSize() > 0){
			foreach($collection as $value){
				$iden	=	$value->getIdentifier();
				$desc	=	$value->getDescription();
				$str	=	unserialize($value->getContent());

				if($str != "")		$value->setContent($helper->emmenu_encode($str));
				if($iden == "" )	$value->setIdentifier("autoupdate_".$value->getId()."_".strtolower(str_replace(" ","_",$value->getName())));
				if($desc == "" )	$value->setDescription($helper->__("Coming soon"));

				$value->save();
			}
		}

		return true;
    }

}