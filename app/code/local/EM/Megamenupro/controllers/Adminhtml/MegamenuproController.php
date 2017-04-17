<?php
class EM_Megamenupro_Adminhtml_MegamenuproController extends Mage_Adminhtml_Controller_Action
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
		if(Mage::getSingleton('admin/session')->isAllowed('emthemes/megamenupro')){
			$this->_initAction()
				->renderLayout();
		}else{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenupro')->__("You don't have permission to save item. Maybe this is a demo store."));
			$this->_redirect('*/dashboard/');
		}
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

			$this->_addContent($this->getLayout()->createBlock('megamenupro/adminhtml_megamenupro_edit'))
				->_addLeft($this->getLayout()->createBlock('megamenupro/adminhtml_megamenupro_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenupro')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
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
					$this->_redirect('*/*/');
					return;
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					Mage::getSingleton('adminhtml/session')->setFormData($data);
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}
			}
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenupro')->__('Unable to find item to save'));
		}else{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenupro')->__("You don't have permission to save item. Maybe this is a demo store."));
		}
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if(Mage::getSingleton('admin/session')->isAllowed('emthemes/megamenupro/delete')){
			if( $this->getRequest()->getParam('id') > 0 ) {
				try {
					$model = Mage::getModel('megamenupro/megamenupro');
					 
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
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenupro')->__("You don't have permission to delete item. Maybe this is a demo store."));
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
		if(Mage::getSingleton('admin/session')->isAllowed('emthemes/megamenupro/delete')){
			$megamenuproIds = $this->getRequest()->getParam('megamenupro');
			if(!is_array($megamenuproIds)) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
			} else {
				try {
					foreach ($megamenuproIds as $megamenuproId) {
						$megamenupro = Mage::getModel('megamenupro/megamenupro')->load($megamenuproId);
						$megamenupro->delete();
					}
					Mage::getSingleton('adminhtml/session')->addSuccess(
						Mage::helper('adminhtml')->__(
							'Total of %d record(s) were successfully deleted', count($megamenuproIds)
						)
					);
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				}
			}
		} else
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenupro')->__("You don't have permission to delete item. Maybe this is a demo store."));
        $this->_redirect('*/*/index');
    }

    public function massStatusAction(){
		if(Mage::getSingleton('admin/session')->isAllowed('emthemes/megamenupro/save')){
			$megamenuproIds = $this->getRequest()->getParam('megamenupro');
			if(!is_array($megamenuproIds)) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
			} else {
				try {
					foreach ($megamenuproIds as $megamenuproId) {
						$megamenupro = Mage::getSingleton('megamenupro/megamenupro')
							->load($megamenuproId)
							->setStatus($this->getRequest()->getParam('status'))
							->setIsMassupdate(true)
							->save();
					}
					$this->_getSession()->addSuccess(
						$this->__('Total of %d record(s) were successfully updated', count($megamenuproIds))
					);
				} catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		} else
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenupro')->__("You don't have permission to save item. Maybe this is a demo store."));
        $this->_redirect('*/*/index');
    }

    public function flushcacheAction()
    {
		if(Mage::getSingleton('admin/session')->isAllowed('emthemes/megamenupro/save')){
			Mage::helper("megamenupro")->getFlushCache('all');
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('megamenupro')->__('The megamenupro cache has been flushed.'));
		} else
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenupro')->__("You don't have permission to save item. Maybe this is a demo store."));
		$this->_redirect('*/*/');
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
