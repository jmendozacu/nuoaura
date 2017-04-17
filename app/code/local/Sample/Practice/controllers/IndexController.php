<?php

class Sample_Practice_IndexController extends Mage_Core_Controller_Front_Action {        
    public function indexAction() {
        
        $this->loadLayout();
        $this->renderLayout();


    }

    public function byeAction(){
                
                 $this->loadLayout();
                 $this->renderLayout();
            }
}

?>
