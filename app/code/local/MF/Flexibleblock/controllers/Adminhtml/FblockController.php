<?php
class MF_Flexibleblock_Adminhtml_FblockController extends Mage_Adminhtml_Controller_Action
{
    public function _initFblock()
    {
        $id = $this->getRequest()->getParam('id');
		$fblock = Mage::getModel('flexibleblock/fblock');
        // Initial checking
        if ($id) {
            $storeId = $this->getRequest()->getParam('store',Mage_Core_Model_App::ADMIN_STORE_ID);
            $fblock->setStoreId($storeId)->load($id);
        }
        if(Mage::registry('fblock_data'))
			Mage::unregister ('fblock_data');
		Mage::register('fblock_data', $fblock);
        return Mage::registry('fblock_data');
    }

    /**
     * Convert actions from array to string
     *
     * @param array
     * @return string
     */
    public function initConditions($actions)
    {
        $rule = Mage::getModel('catalogrule/rule');

        $rule->loadPost($actions);

        if ($rule->getConditions()) {
            $rule->setConditionsSerialized(serialize($rule->getConditions()->asArray()));
            $rule->unsConditions();
        }

        return $rule->getConditionsSerialized();
    }
    /**
     * Initialize product before saving
     */
    protected function _initFblockSave()
    {
        $fblock     = $this->_initFblock();
        $fblockData = $this->getRequest()->getPost();
        /* Check change position */
        /*if($fblock->getId()){
            $oldPosition = $fblock->getCustomPosition() ? $fblock->getCustomPosition() : $fblock->getPosition();
            $newPosition = isset($fblockData['custom_position']) ? $fblockData['custom_position'] : isset($fblockData['position']) ? $fblockData['position'] : '';
            if($oldPosition != $newPosition){
                $fblock->setOldPosition($oldPosition);
            }
        }*/
        if(!isset($fblockData['order']))
            $fblockData['order'] = 0;
        $fblock->addData($fblockData);
        /**
         * Initialize product categories
         */
        $categoryIds = $this->getRequest()->getPost('category_ids');
        $fblock->setCategoryIds($categoryIds);

        /**
         * Init actions
         */
        $fblock->setConditions($this->initConditions($fblockData['rule']));

        /**
         * Check "Use Default Value" checkboxes values
         */
        if ($useDefaults = $this->getRequest()->getPost('use_default')) {

            foreach ($useDefaults as $attributeCode) {
                $fblock->setData($attributeCode, false);
            }
        }

        /*if (null !== $categoryIds) {
            if (empty($categoryIds)) {
                $categoryIds = array();
            }
            $fblock->setCategoryIds($categoryIds);
        } else {
            $fblock->setCategoryIds(null);
        }*/

        /* Set identifier */
        if(!$fblock->getIdentifier()){
            $fblock->setIdentifier(Mage::helper('flexibleblock')->friendlyIdentifier($fblock->getTitle()));
        } else {
            $fblock->setIdentifier(Mage::helper('flexibleblock')->friendlyIdentifier($fblock->getIdentifier()));
        }
        return $fblock;
    }

    protected function _initAction() {
            $this->loadLayout()
                    ->_setActiveMenu('cms/block_manager')
                    ->_addBreadcrumb(Mage::helper('adminhtml')->__('Block Manager'), Mage::helper('adminhtml')->__('Block Manager'));

            return $this;
    }

    public function indexAction() {
        /*$collection = Mage::getResourceModel('flexibleblock/fblock_collection');
        if($collection->getSize() > 0){
            foreach($collection as $fblock){
                $fblock->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)->setDisplayPc(1)->setDisplayTablet(1)->setDisplayMobile(1);
                $resource = $fblock->getResource();
                $resource->saveAttribute($fblock, 'display_pc');
                $resource->saveAttribute($fblock, 'display_tablet');
                $resource->saveAttribute($fblock, 'display_mobile');
            }
        }*/
            $this->_initAction();
            $this->getLayout()->getBlock('head')->setTitle($this->__('Manage Blocks'));
            $this->renderLayout();
    }

   
    public function editAction() {
        $this->_title($this->__('CMS'))->_title($this->__('Flexible Block'));

        // 1. Get ID and create model and Initial checking
        $id = $this->getRequest()->getParam('id');
        $fblock = $this->_initFblock();
        if(!$fblock->getId()){
            $fblock->setDisplayPc(1)->setDisplayTablet(1)->setDisplayMobile(1);
        }
        //echo '<pre>';print_r($fblock->getData());exit;
        // 2. Initial checking
        if ($id && !$fblock->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexibleblock')->__('This block no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $this->_title($fblock->getId() ? $fblock->getTitle() : $this->__('New Block'));

        //3. Add content left, main content
        $this->_initAction()->_addBreadcrumb($fblock->getId() ? Mage::helper('cms')->__('Edit Block') : Mage::helper('cms')->__('New Block'), $id ? Mage::helper('cms')->__('Edit Block') : Mage::helper('cms')->__('New Block'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        if($fblock->getId())
            $this->_addLeft($this->getLayout()->createBlock('adminhtml/store_switcher'));
        $this->_addContent($this->getLayout()->createBlock('flexibleblock/adminhtml_fblock_edit'))
                ->_addLeft($this->getLayout()->createBlock('flexibleblock/adminhtml_fblock_edit_tabs'));

        $this->renderLayout();
    }

    public function newAction() {
        $this->_forward('edit');
    }

    /**
     * Get categories fieldset block
     *
     */
    public function categoriesAction()
    {
        $this->_initFblock();
        $this->loadLayout();
        $this->renderLayout();
    }

    public function categoriesJsonAction()
    {
        $this->_initFblock();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('flexibleblock/adminhtml_fblock_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    public function validateAction(){
        $postData = $this->getRequest()->getPost();
        $response = new Varien_Object();
        if(!$postData['identifier'])
            $identifier = Mage::helper('flexibleblock')->friendlyIdentifier($postData['title']);
        else
            $identifier = Mage::helper('flexibleblock')->friendlyIdentifier($postData['identifier']);

        $collection = Mage::getResourceModel('flexibleblock/fblock_collection')->addAttributeToFilter('identifier',$identifier);
        $id = $this->getRequest()->getParam('id');
        if($id){
            $collection->addAttributeToFilter('entity_id',array('neq' => $id));
        }

        if($collection->getSize() > 0){
            $response->setError(true);
            $response->setAttribute("fblock_identifier");
            $response->setMessage(Mage::helper('flexibleblock')->__("The value of identifier is unique"));
        }
        else
            $response->setError(false);
        $this->getResponse()->setBody($response->toJson());
    }

    public function positionAction(){
        $data = $this->getRequest()->getPost();
        $positionKeyList = Mage::helper('flexibleblock')->getPositionKeyList(true);
        $selectedKeyList = Mage::helper('flexibleblock')->getSelectedKeyList();
        $disabledKeyList = Mage::helper('flexibleblock')->getDisabledKeyList();

        $positionLoad = explode(',',$data['array_load']);
        $result = array();
        foreach($positionKeyList as $handleKey => $positionKey){
            if(in_array($positionKey,$positionLoad)){
                $selectedKey = $selectedKeyList[$handleKey];
                $disabledKey = $disabledKeyList[$handleKey];
                $disabled = isset($data[$disabledKey]) ? $data[$disabledKey] : '';
                $block = $this->getLayout()->createBlock('flexibleblock/adminhtml_fblock_edit_tab_general_position')
                    ->setPackage($data['package'])
                    ->setTheme($data['theme'])
                    ->setSelected($data[$selectedKey])
                    ->setLayoutHandle($data[$handleKey])
                    ->setDisabled($disabled)
                    ->setName($positionKey)
                    ->setId('fblock_'.$positionKey);
                $result[$positionKey] = $block->toHtml();
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    public function handleAction(){
        $data = $this->getRequest()->getPost();
        $positionKeyList = Mage::helper('flexibleblock')->getPositionKeyList(true);
        $selectedKeyList = Mage::helper('flexibleblock')->getSelectedKeyList();
        $disabledKeyList = Mage::helper('flexibleblock')->getDisabledKeyList();
        $result = array();
        foreach($positionKeyList as $handleKey => $positionKey){
            $selectedKey = $selectedKeyList[$handleKey];
            $disabledKey = $disabledKeyList[$handleKey];
            $disabled = isset($data[$disabledKey]) ? $data[$disabledKey] : '';
            $block = $this->getLayout()->createBlock('flexibleblock/adminhtml_fblock_edit_tab_general_handle')
                ->setPackage($data['package'])
                ->setTheme($data['theme'])
                ->setSelected($data[$selectedKey])
                ->setDisabled($disabled)
                ->setName($handleKey)
                ->setExtraParams("onchange=\"changeOneHandle('".$positionKey."');\"")
                ->setId('fblock_'.$handleKey);
            $result[$handleKey] = $block->toHtml();
        }

        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    public function saveAction() {
		if(Mage::getSingleton('admin/session')->isAllowed('cms/block_manager/save')){
			if ($data = $this->getRequest()->getPost()) {
				$fblock = $this->_initFblockSave();
			 try {
					$fblock->save();
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexibleblock')->__('Block was successfully saved'));
					Mage::getSingleton('adminhtml/session')->setFormData(false);

					if ($this->getRequest()->getParam('back')) {
							$this->_redirect('*/*/edit', array('id' => $fblock->getEntityId(),'store'=>$this->getRequest()->getParam('store',0),'_current'=>true));

							return;
					}
					$this->_redirect('*/*/');
					return;
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
					Mage::getSingleton('adminhtml/session')->setFormData($data);
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'),'store'=>$this->getRequest()->getParam('store',0)));
					return;
				}
			}
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexibleblock')->__("You don't have permission to save block. Maybe this is a demo store."));
		}
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexibleblock')->__('Unable to find block to save'));
        $this->_redirect('*/*/');
    }

    /* Duplicate flexible block */
    public function duplicateAction(){
        if(Mage::getSingleton('admin/session')->isAllowed('cms/block_manager/save')){
            $id = $this->getRequest()->getParam('id');
            $block = Mage::getModel('flexibleblock/fblock')->load($id);
            if($block->getId()){
                $block->setStoreId(Mage::app()->getStore()->getId())->setId(null)->setIdentifier(null);
                $block->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flexibleblock')->__('Block was successfully duplicated'));
                $this->_redirect('*/*/edit', array('id' => $block->getId(),'store'=>$this->getRequest()->getParam('store',0)));
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexibleblock')->__('Unable to find block to duplicate'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexibleblock')->__("You don't have permission to save block. Maybe this is a demo store."));
            $this->_redirect('*/*/');
            return;
        }
    }

    public function deleteAction() {
		if(Mage::getSingleton('admin/session')->isAllowed('cms/block_manager/delete')){
            $id = $this->getRequest()->getParam('id');
			if( $id > 0 ) {
				try {
					$fblock = Mage::getModel('flexibleblock/fblock')->load($id);
					$fblock->delete();
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Block was successfully deleted'));
						$this->_redirect('*/*/');
					} catch (Exception $e) {
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id'),'_current'=>true));
					}
			}
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexibleblock')->__("You don't have permission to delete block. Maybe this is a demo store."));
		}
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
		if(Mage::getSingleton('admin/session')->isAllowed('cms/block_manager/delete')){
			$ids = $this->getRequest()->getParam('fblock');
			if(!is_array($ids)) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
			} else {
				try {
					foreach ($ids as $id) {
						$model = Mage::getModel('flexibleblock/fblock')->load($id);
						$model->delete();
					}
					Mage::getSingleton('adminhtml/session')->addSuccess(
						Mage::helper('adminhtml')->__(
							'Total of %d record(s) were successfully deleted', count($ids)
						)
					);
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				}
			}
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexibleblock')->__("You don't have permission to delete block. Maybe this is a demo store."));
		}
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
		if(Mage::getSingleton('admin/session')->isAllowed('cms/block_manager/save')){
			$ids = $this->getRequest()->getParam('fblock');
			$storeId = $this->getRequest()->getParam('store',Mage_Core_Model_App::ADMIN_STORE_ID);
			if(!is_array($ids)) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
			} else {
				try {
					foreach ($ids as $id) {
						$model = Mage::getSingleton('flexibleblock/fblock')->setStoreId($storeId)
							->load($id)
							->setStatus($this->getRequest()->getParam('status'))
							->setIsMassupdate(true)
							->save();
					}
					$this->_getSession()->addSuccess(
						$this->__('Total of %d record(s) were successfully updated', count($ids))
					);
				} catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flexibleblock')->__("You don't have permission to save block. Maybe this is a demo store."));
		}
        $this->_redirect('*/*/index',array('_current'=>true));
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'fblock.csv';
        $content    = $this->getLayout()->createBlock('flexibleblock/adminhtml_fblock_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'fblock.xml';
        $content    = $this->getLayout()->createBlock('flexibleblock/adminhtml_fblock_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
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