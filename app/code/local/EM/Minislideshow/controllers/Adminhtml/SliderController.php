<?php
class EM_Minislideshow_Adminhtml_SliderController extends Mage_Adminhtml_Controller_Action
{
	/**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('emthemes/minislideshow/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('emthemes/minislideshow/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('emthemes/minislideshow');
                break;
        }
    }

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('cms/minislideshow/slider')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Slideshow Manager'), Mage::helper('adminhtml')->__('Slideshow Manager'));
		
		return $this;
	}   

	public function indexAction() {
		if(Mage::getSingleton('admin/session')->isAllowed('emthemes/minislideshow')){
			$this->_initAction()
				->renderLayout();
		}else{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('minislideshow')->__("You don't have permission to save item. Maybe this is a demo store."));
			$this->_redirect('*/dashboard/');
		}
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$helper = Mage::helper("minislideshow");
		$model  = Mage::getModel('minislideshow/slider')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			if(!$id){ // set default value
				$model->setData('status_slideshow',1);
				$model->setData('size_width',960);
				$model->setData('size_height',350);
			}else{
				$info	=	$model->getData();

				$slider_params	=	$helper->emslider_decode($model->getData('slider_params'));
				$appearance	=	$helper->emslider_decode($model->getData('appearance'));
				$navigation	=	$helper->emslider_decode($model->getData('navigation'));
				$info	=	array_merge($info,$slider_params,$appearance,$navigation);
				$info['status_slideshow']	=	$info['status'];
				unset($info['slider_params'],$info['appearance'],$info['navigation'],$info['status']);

				$model->setData($info);
			}

			Mage::register('minislideshow_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('cms/minislideshow/slider');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Slideshow Manager'), Mage::helper('adminhtml')->__('Slideshow Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Slideshow News'), Mage::helper('adminhtml')->__('Slideshow News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('minislideshow/adminhtml_slider_edit'))
				->_addLeft($this->getLayout()->createBlock('minislideshow/adminhtml_slider_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('minislideshow')->__('Slideshow does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if(Mage::getSingleton('admin/session')->isAllowed('emthemes/minislideshow/save')){
			if ($data = $this->getRequest()->getPost()) {
				$helper = Mage::helper("minislideshow");
				$path = Mage::getBaseDir('media') . DS . 'em_minislideshow' . DS;
				//echo '<pre>';print_r($data);exit;
				$data['slider_params']	=	$helper->emslider_encode($data['slider_params']);
				$data['appearance']		=	$helper->emslider_encode($data['appearance']);
				$data['navigation']		=	$helper->emslider_encode($data['navigation']);
				unset($data['page'],$data['limit'],$data['in_products'],$data['id'],$data['image'],$data['link'],$data['status'],$data['position_images']);
				$data['status']			=	$data['status_slideshow'];
				$tmp_img	=	$data['images'];
				$old_img	=	$data['deloldimg'];
				unset($data['status_slideshow'],$data['images'],$data['deloldimg']);

				$plit	=	explode("_!_",$old_img);
				if(count($plit) > 1){
					foreach($plit as $pkey=>$pval){
						if($pkey > 0){
						  /*
							if(is_file($path.$pval))
								unlink($path.$pval);*/
							/* Remove old image resize */
							if(is_file($path. 'resize' . DS . '55x55' . DS . $pval))
								unlink($path. 'resize' . DS . '55x55' . DS . $pval);
						}
					}
				}

				$i=0;foreach($_FILES['files']['type'] as $key=>$val){
					$filecheck = basename($_FILES['files']['name'][$key]);
					$ext = strtolower(substr($filecheck, strrpos($filecheck, '.') + 1));
					
					if (!(($ext == "jpg" || $ext == "gif" || $ext == "png") && ($val == "image/jpg" ||$val == "image/jpeg" || $val == "image/gif" || $val == "image/png") && ($_FILES["files"]["size"][$key] < 3000000) )){
						if($tmp_img[$key]['url'] == "")
							unset($tmp_img[$key]);
					}else{
						if ($_FILES["files"]["error"][$key] == UPLOAD_ERR_OK) {
							if($tmp_img[$key]['url'] != ""){
							 /*
								if(is_file($path.$tmp_img[$key]['url']))
									unlink($path.$tmp_img[$key]['url']);*/
								/* Remove old image resize */
								if(is_file($path. 'resize' . DS . '55x55' . DS . $tmp_img[$key]['url']))
									unlink($path. 'resize' . DS . '55x55' . DS . $tmp_img[$key]['url']);
							}
							$date	=	getdate();
							$file_name	=	$date[0].'_'.$i++.'_'.$_FILES["files"]["name"][$key];
							$file_name	=	preg_replace('/[^a-zA-Z0-9\-_.]/','',$file_name);
							$tmp_img[$key]['url']	=	$file_name;
							
							$tmp_file	=	 $_FILES["files"]["tmp_name"][$key];
							move_uploaded_file($tmp_file, "$path/$file_name");
						}
					}
					
				}
				foreach ($tmp_img as $key => $row) {
					$position[$key]  = intval($row['position']);
				}
				array_multisort($position, SORT_ASC, $tmp_img);
				$data['images']	=	$helper->emslider_encode($tmp_img);

				$model = Mage::getModel('minislideshow/slider');
				$model->setData($data);
				if($id = $this->getRequest()->getParam('id'))
					$model->setId($id);
					//->setId($this->getRequest()->getParam('id',null));

				try {
					if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
						$model->setCreatedTime(now())
							->setUpdateTime(now());
					} else {
						$model->setUpdateTime(now());
					}

					$model->save();

					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('minislideshow')->__('Slideshow was successfully saved'));
					Mage::getSingleton('adminhtml/session')->setFormData(false);

					if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('id' => $model->getId()));
						return;
					}
					$this->_redirect('*/*/');
					return;
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					Mage::getSingleton('adminhtml/session')->setFormData($data);
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}
			}
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('minislideshow')->__('Unable to find item to save'));
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('minislideshow')->__("You don't have permission to save item. Maybe this is a demo store."));
		}

        $this->_redirect('*/*/');
	}

	public function deleteAction() {
		if(Mage::getSingleton('admin/session')->isAllowed('emthemes/minislideshow/delete')){
			if( $this->getRequest()->getParam('id') > 0 ) {
				try {
					$model = Mage::getModel('minislideshow/slider');
					/* $this->deleted_images($this->getRequest()->getParam('id')); */
					$model->setId($this->getRequest()->getParam('id'))
						->delete();
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
					$this->_redirect('*/*/');
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				}
			}
		} else 
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('minislideshow')->__("You don't have permission to delete item. Maybe this is a demo store."));
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
		if(Mage::getSingleton('admin/session')->isAllowed('emthemes/minislideshow/delete')){
			$minislideshowIds = $this->getRequest()->getParam('minislideshow');
			if(!is_array($minislideshowIds)) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
			} else {
				try {
					foreach ($minislideshowIds as $minislideshowId) {
						/* $this->deleted_images($minislideshowId); */
						$minislideshow = Mage::getModel('minislideshow/slider')->load($minislideshowId);
						$minislideshow->delete();
					}
					Mage::getSingleton('adminhtml/session')->addSuccess(
						Mage::helper('adminhtml')->__(
							'Total of %d record(s) were successfully deleted', count($minislideshowIds)
						)
					);
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				}
			}
		} else 
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('minislideshow')->__("You don't have permission to delete item. Maybe this is a demo store."));
        $this->_redirect('*/*/index');
    }
	
	public function deleted_images($id){
		$path = Mage::getBaseDir('media') . DS . 'em_minislideshow' . DS;
		$slider = Mage::getModel('minislideshow/slider')->load($id);
		$images	=	Mage::helper("minislideshow")->emslider_decode($slider->getImages());
		foreach($images as $img){
			if(is_file($path.$img['url']))
				unlink($path.$img['url']);
			/* Remove old image resize */
			if(is_file($path. 'resize' . DS . '55x55' . DS . $img['url']))
				unlink($path. 'resize' . DS . '55x55' . DS . $img['url']);
		}
	}

    public function massStatusAction(){
		if(Mage::getSingleton('admin/session')->isAllowed('emthemes/minislideshow/save')){
			$minislideshowIds = $this->getRequest()->getParam('minislideshow');
			if(!is_array($minislideshowIds)) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
			} else {
				try {
					foreach ($minislideshowIds as $minislideshowId) {
						$minislideshow = Mage::getSingleton('minislideshow/slider')
							->load($minislideshowId)
							->setStatus($this->getRequest()->getParam('status'))
							->setIsMassupdate(true)
							->save();
					}
					$this->_getSession()->addSuccess(
						$this->__('Total of %d record(s) were successfully updated', count($minislideshowIds))
					);
				} catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('minislideshow')->__("You don't have permission to save item. Maybe this is a demo store."));
		}
        $this->_redirect('*/*/index');
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
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