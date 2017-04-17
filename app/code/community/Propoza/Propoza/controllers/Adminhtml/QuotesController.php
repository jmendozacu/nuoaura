<?php

class Propoza_Propoza_Adminhtml_QuotesController extends Mage_Adminhtml_Controller_Action {
    public function indexAction() {
        $this->loadLayout()->_setActiveMenu('quotes')->_title($this->__('Quotes'));

        $this->renderLayout();
    }
}