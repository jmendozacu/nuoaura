<?php
class MF_Flexibleblock_Block_Adminhtml_Element_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_element')
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('flexibleblock/adminhtml_element_fieldset')
        );
    }
	
	public function getAttributes($entity,$key = 'fblock'){
		if(!Mage::registry($key)){
			$attributes = Mage::getModel('flexibleblock/attribute')->getCollection()
			->addFieldToFilter('entity_type_id',$entity->getResource()->getEntityType()->getEntityTypeId())
			->setOrder('position','ASC');
            Mage::register($key, $attributes);
			$this->setAttrCodeArray($key.'_attr_code_array',$attributes);
		}
		return Mage::registry($key);	
	}
	
	public function setAttrCodeArray($key,$attributes){
		if(!Mage::registry($key)){
			$result = array();
			foreach($attributes as $a){
				$result[] = $a->getAttributeCode();
			}
			Mage::register($key,$result);
		}
		return Mage::registry($key);
	}
	
	public function getAttrCodeArray($key){
		return Mage::registry($key);
	}
	
	 /**
     * Set Fieldset to Form
     *
     * @param array $attributes attributes that are to be added
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $exclude attributes that should be skipped
     */
    protected function _setFieldset($attributes, $fieldset, $exclude=array())
    {
        $this->_addElementTypes($fieldset);
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if (!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible())) {
                continue;
            }
            if ( ($inputType = $attribute->getFrontend()->getInputType())
                 && !in_array($attribute->getAttributeCode(), $exclude)
                 && ('media_image' != $inputType)
                 ) {
                $fieldType      = $inputType;
                $rendererClass  = $attribute->getFrontend()->getInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType  = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }

				$dataField = array(
                        'name'      => $attribute->getAttributeCode(),
                        'label'     => $attribute->getFrontend()->getLabel(),
                        'class'     => $attribute->getFrontend()->getClass(),
                        'required'  => $attribute->getIsRequired(),
                        'note'      => $attribute->getNote(),
                    );
				if($fieldType == 'editor'){
					$dataField['style'] = 'height:36em';
					$dataField['config'] = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
					//$fieldType = 'textarea';
                    //echo '<pre>';print_r($dataField);exit;
				}	
                $element = $fieldset->addField($attribute->getAttributeCode(), $fieldType,
                    $dataField
                )
                ->setEntityAttribute($attribute);

                $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));
                if ($inputType == 'select') {
                    $element->setValues($attribute->getSource()->getAllOptions(true, true));
                } else if ($inputType == 'multiselect') {
                    $element->setValues($attribute->getSource()->getAllOptions(false, true));
                    $element->setCanBeEmpty(true);
                } else if ($inputType == 'date') {
                    $element->setImage($this->getSkinUrl('images/grid-cal.gif'));
                    $element->setFormat(Mage::app()->getLocale()->getDateFormat());
                } else if ($inputType == 'multiline') {
                    $element->setLineCount($attribute->getMultilineCount());
                }
            }
        }
    }
}

?>
