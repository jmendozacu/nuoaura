<?php
class EM_Ajaxblock_IndexController extends Mage_Core_Controller_Front_Action{
	
    public function indexAction() {
        $data = $this->getRequest()->getParam('data');
        $data = base64_decode($data);
        $data = json_decode($data);
        $result = array();
        foreach($data as $key => $value)
        {
            $result[$key] = $value;
        }
        $block = $this->getLayout()->createBlock($result['type']);
        $block->setData($result);
        if($result['type'] == 'tabs/group')
        {
            $block->setTemplate('emtabs/group.phtml');
        }
        echo $block->toHtml();
        die;
    }
}
