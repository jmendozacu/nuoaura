<?php
class MF_Flexibleblock_Block_Adminhtml_Fblock_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'flexibleblock';
        $this->_controller = 'adminhtml_fblock';
        $id = $this->getRequest()->getParam($this->_objectId,0);
        $this->setValidationUrl($this->getUrl('*/*/validate',array('id'=>$id)));
		if(Mage::getSingleton('admin/session')->isAllowed('cms/block_manager/save')){
			$this->_updateButton('save', 'label', Mage::helper('flexibleblock')->__('Save Block'));
			$this->_addButton('saveandcontinue', array(
				'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
				'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
				'class'     => 'save',
			), -100);
            if($id){
                $this->_addButton('duplicate', array(
                    'label'     => Mage::helper('adminhtml')->__('Duplicate'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/duplicate', array($this->_objectId => $this->getRequest()->getParam($this->_objectId))).'\')',
                    'class'     => 'duplicate',
                ), 0);
            }

		} else {
			$this->_removeButton('save');
		}

		if(Mage::getSingleton('admin/session')->isAllowed('cms/block_manager/delete')){	
			$this->_updateButton('delete', 'label', Mage::helper('flexibleblock')->__('Delete Block'));
		} else {
			$this->_removeButton('delete');
		}
    }

    public function getHeaderText()
    {
        if( Mage::registry('fblock_data') && Mage::registry('fblock_data')->getId() ) {
            return Mage::helper('flexibleblock')->__("Edit Block '%s'", $this->escapeHtml(Mage::registry('fblock_data')->getTitle()));
        } else {
            return Mage::helper('flexibleblock')->__('Add Block');
        }
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'active_tab' => '{{tab_id}}'
        ));
    }

    /**
     * Prepare layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $tabsBlockJsObject = 'fblock_tabsJsTabs';
        $tabsBlockPrefix   = 'fblock_tabs_';
        $positionUrl = $this->getUrl('*/*/position', array('_current' => true, 'id' => '','package' => '','theme' => ''));
        $handleUrl = $this->getUrl('*/*/handle', array('_current' => true, 'id' => '','package' => '','theme' => ''));

        $position = Mage::registry('fblock_data')->getData('position');
        $position2 = Mage::registry('fblock_data')->getData('position_2');
        $position3 = Mage::registry('fblock_data')->getData('position_3');
        $layoutHandle = Mage::registry('fblock_data')->getData('layout_handle');
        $layoutHandle2 = Mage::registry('fblock_data')->getData('layout_handle_2');
        $layoutHandle3 = Mage::registry('fblock_data')->getData('layout_handle_3');
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('fblock_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'fblock_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'fblock_content');
                }
            }

            function saveAndContinueEdit(urlTemplate) {
                var tabsIdValue = " . $tabsBlockJsObject . ".activeTab.id;
                var tabsBlockPrefix = '" . $tabsBlockPrefix . "';
                if (tabsIdValue.startsWith(tabsBlockPrefix)) {
                    tabsIdValue = tabsIdValue.substr(tabsBlockPrefix.length)
                }

                var template = new Template(urlTemplate, /(^|.|\\r|\\n)({{(\w+)}})/);
                var url = template.evaluate({tab_id:tabsIdValue});
                //console.log(url);return false;
                editForm.submit(url);
            }

            editForm._processValidationResult = function(transport) {
                var response = transport.responseText.evalJSON();
                if (response.error){
                    if (response.attribute && $(response.attribute)) {
                        $(response.attribute).setHasError(true, editForm);
                        Validation.ajaxError($(response.attribute), response.message);
                        if (!Prototype.Browser.IE){
                            $(response.attribute).focus();
                        }
                    }
                    else if ($('messages')) {
                        $('messages').innerHTML = '<ul class=\"messages\"><li class=\"error-msg\"><ul><li>' + response.message + '</li></ul></li></ul>';
                    }
                }
                else{
                    editForm._submit();
                }
            };

            function reloadPosition(package,theme, selected, selected_2, selected_3, arrayLoad){
                var postParameters = \$H({
                    package:package,
                    theme:theme,
                    selected:selected,
                    selected_2:selected_2,
                    selected_3:selected_3,
                    layout_handle:$('fblock_layout_handle').value,
                    layout_handle_2:$('fblock_layout_handle_2').value,
                    layout_handle_3:$('fblock_layout_handle_3').value,
                    array_load : arrayLoad,
                    form_key: FORM_KEY
                });
                var ajaxPositionUrl = '{$positionUrl}';

                if($('fblock_position').disabled == true){
                    postParameters.set('disabled',1);
                }
                if($('fblock_position_2').disabled == true){
                    postParameters.set('disabled_2',1);
                }
                if($('fblock_position_3').disabled == true){
                    postParameters.set('disabled_3',1);
                }
                new Ajax.Request(ajaxPositionUrl, {
                    method: 'post',
                    parameters: postParameters,
                    onSuccess: function(transport) {
                        if (transport.responseText.isJSON()) {
                            var response = transport.responseText.evalJSON()
                            if (response.error) {
                                alert(response.message);
                                return;
                            }
                            if(response.ajaxExpired && response.ajaxRedirect) {
                                setLocation(response.ajaxRedirect);
                                return;
                            }
                            Object.keys(response).forEach(function(key) {
                                $('fblock_' + key).replace(response[key]);
                            });
                        }
                    }
                });
            }

            function changeOneHandle(positionKey){
                if($('fblock_package_theme').value != ''){
                    var list = $('fblock_package_theme').value.split('/');
                    var package = list[0];
                    var theme = list[1];
                    var selected = $('fblock_position').value;
                    var selected_2 = $('fblock_position_2').value;
                    var selected_3 = $('fblock_position_3').value;
                    reloadPosition(package, theme, selected, selected_2, selected_3,positionKey);
                }
            }

            function reloadHandle(package, theme, layout, layout_2, layout_3, position, position_2, position_3){
                 var postParameters = \$H({
                    package:package,
                    theme:theme,
                    selected:layout,
                    selected_2:layout_2,
                    selected_3:layout_3,
                    form_key: FORM_KEY
                });
                if($('fblock_layout_handle').disabled == true){
                    postParameters.set('disabled',1);
                }
                if($('fblock_layout_handle_2').disabled == true){
                    postParameters.set('disabled_2',1);
                }
                if($('fblock_layout_handle_3').disabled == true){
                    postParameters.set('disabled_3',1);
                }
                var ajaxHandleUrl = '{$handleUrl}';
                new Ajax.Request(ajaxHandleUrl, {
                    method: 'post',
                    parameters: postParameters,
                    onSuccess: function(transport) {
                        if (transport.responseText.isJSON()) {
                            var response = transport.responseText.evalJSON()
                            if (response.error) {
                                alert(response.message);
                                return;
                            }
                            if(response.ajaxExpired && response.ajaxRedirect) {
                                setLocation(response.ajaxRedirect);
                                return;
                            }
                            $('fblock_layout_handle').replace(response.layout_handle);
                            $('fblock_layout_handle_2').replace(response.layout_handle_2);
                            $('fblock_layout_handle_3').replace(response.layout_handle_3);
                            var positionArrayLoad = [];
                            if($('fblock_layout_handle').value)
                                positionArrayLoad.push('position');
                            if($('fblock_layout_handle_2').value)
                                positionArrayLoad.push('position_2');
                            if($('fblock_layout_handle_3').value)
                                positionArrayLoad.push('position_3');
                            if(positionArrayLoad.length > 0){
                                reloadPosition(package, theme, position, position_2, position_3, positionArrayLoad.join());
                            }
                        }
                    }
                });
            }

            $('fblock_package_theme').observe('change',function(){
                if($(this).value != ''){
                    var list = $(this).value.split('/');
                    var package = list[0];
                    var theme = list[1];
                    var position = $('fblock_position').value;
                    var position_2 = $('fblock_position_2').value;
                    var position_3 = $('fblock_position_3').value;
                    var layout = $('fblock_layout_handle').value;
                    var layout_2 = $('fblock_layout_handle_2').value;
                    var layout_3 = $('fblock_layout_handle_3').value;
                    reloadHandle(package, theme, layout, layout_2, layout_3, position, position_2, position_3);
                }
            });

            /* Load position list when loaded page */
            document.observe('dom:loaded', function() {
                if($('fblock_package_theme').value != ''){
                    var list = $('fblock_package_theme').value.split('/');
                    var package = list[0];
                    var theme = list[1];
                    var layout = '{$layoutHandle}';
                    var layout_2 = '{$layoutHandle2}';
                    var layout_3 = '{$layoutHandle3}';
                    var position = '{$position}';
                    var position_2 = '{$position2}';
                    var position_3 = '{$position3}';
                    reloadHandle(package, theme, layout, layout_2, layout_3, position, position_2, position_3);
                }
            });
        ";
        return parent::_prepareLayout();
    }
}