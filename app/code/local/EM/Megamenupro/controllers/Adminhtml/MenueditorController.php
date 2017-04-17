<?php
class EM_Megamenupro_Adminhtml_MenueditorController extends Mage_Adminhtml_Controller_Action
{
	protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('emthemes/megamenupro/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('emthemes/megamenupro/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('emthemes/megamenupro');
                break;
        }
    }

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('megamenupro/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('megamenupro/megamenupro')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('megamenupro_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('megamenupro/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

			$this->_addContent($this->getLayout()->createBlock('megamenupro/adminhtml_menueditor_edit'));
			if (!$model->getId())
				$this->_addLeft($this->getLayout()->createBlock('megamenupro/adminhtml_mnueditor_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenupro')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function parsecodeAction(){
		$param	=	$this->getRequest()->getParam('menu');
		if(isset($param)){
			foreach($param as $k=>$v){
				if($v['type'] == 'link'){
					$param[$k]['label']		=	$this->filterString($v['label']);
					$param[$k]['sublabel']	=	$this->filterString($v['sublabel']);
				}
				if($v['type'] == 'text')
					$param[$k]['text']	=	base64_encode($v['text']);

				if($v['container_css'] != '')
					$param[$k]['container_css']	=	$this->filterString($v['container_css']);
				if($v['css_class'] != '')
					$param[$k]['css_class']	=	$this->filterString($v['css_class']);

			}
			$data	=	Zend_Json::encode($param);
		}
		else	$data = "";
		print_r($data);exit;
	}

	public function filterString($string){
		if(!$string)	$string == "";
		//$array = array("/'/", "/\"/","/\(/","/\)/");
		$array = array("/\"/");
		return preg_replace($array,"", $string);
	}

	public function saveAction() {
		if(Mage::getSingleton('admin/session')->isAllowed('emthemes/megamenupro/save')){
			if ($data = $this->getRequest()->getPost()) {
				$model = Mage::getModel('megamenupro/megamenupro');
				$model->setData($data)
					->setId($this->getRequest()->getParam('id'));

				try {
					if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
						$model->setCreatedTime(now())
							->setUpdateTime(now());
					} else {
						$model->setUpdateTime(now());
					}

					if ($this->getRequest()->getParam('duplicate')) {
						$model->setName($data['name'].' duplicate menuId '.$this->getRequest()->getParam('id'));
						$model->setIdentifier($data['identifier'].'_duplicate_menuId_'.$this->getRequest()->getParam('id'));
						$model->setId(null);
					}

					$model->save();

					if($this->getRequest()->getParam('duplicate'))
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('megamenupro')->__('Item was successfully duplicate'));
					else{
						if($this->getRequest()->getParam('id'))
							Mage::helper("megamenupro")->getFlushCache($this->getRequest()->getParam('id'));
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('megamenupro')->__('Item was successfully saved'));
					}
					Mage::getSingleton('adminhtml/session')->setFormData(false);

					if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('id' => $model->getId()));
						return;
					}
					$this->_redirect('*/megamenupro/');
					return;
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					Mage::getSingleton('adminhtml/session')->setFormData($data);
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}
			}
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenupro')->__('Unable to find item to save'));
		} else
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenupro')->__("You don't have permission to save item. Maybe this is a demo store."));
        $this->_redirect('*/megamenupro/');
	}

	public function uploadAction(){
		$result['check'] = 0;
		if($_FILES['emicon']){
			$file = $_FILES['emicon'];
			if (($file['type'] == "image/jpeg") || ($file['type'] == "image/jpg") || ($file['type'] == "image/png" ) || ($file['type'] == "image/gif" ))
            {
                if ($file['error'] > 0)
                {
                    $result['msg'] = "Unexpected error occured, please try again later.";
                } else {
					$path = Mage::getBaseDir('media') . DS . 'em' . DS . 'megamenupro' . DS . 'icon';
					if(!is_dir($path)){
						mkdir($path, 0777, true);
					}
					$result['check'] = 1;
					$now 	= Mage::getModel('core/date')->timestamp(time());
					$name = $now.'-'.$this->filterNameImg($file['name']);
					move_uploaded_file($file["tmp_name"], $path."/".$name);

					$result['msg'] = "Upload image Completed";
					$result['name'] = $name;
					$result['img_name'] = Mage::helper("megamenupro")->getResizeImage($name,55,55);
					$result['img_url'] = '{{media url=\\\'em/megamenupro/icon/'.$name.'\\\'}}';
                }
            } else {
                $result['msg'] = "Invalid image format.";
            }
		}else	$result['msg'] = "Please select image file !!!!";
		echo Zend_Json::encode($result);exit;
	}

	public function filterNameImg($string){
		if(!$string)	$string == "";
		$array = array("/'/", "/\"/","/\(/","/\)/","/ /","/\+/");
		return preg_replace($array,"-", $string);
	}

	public function deluploadAction(){
		$check = 0;
		$param = $this->getRequest()->getParams();

		$path = Mage::getBaseDir('media') . DS . 'em' . DS . 'megamenupro' . DS . 'icon'. DS ;
		if(is_file($path.$param['file'])){
			$check = 1;
			unlink($path.$param['file']);
		}
		echo $check;exit;
	}

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream'){
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}
