<?php
class EM_Themeframework_Adminhtml_Themeframework_LinkController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	public function requestinstallationAction() {
		$this->getResponse()->setRedirect('http://www.emthemes.com/magento-installation-services');
	}
	
	public function technicalsupportAction() {
		$this->getResponse()->setRedirect('http://www.codespotsupport.com/magento/');
	}
	
	public function contactusAction(){
		$this->getResponse()->setRedirect('http://www.emthemes.com/contacts');
	}

	public function serviceAction() {
		$this->getResponse()->setRedirect('http://www.emthemes.com/services');
	}
}