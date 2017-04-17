<?php
class MF_Flexibleblock_Model_Fblock_Attribute_Source_Cmspage extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	public function getAllOptions(){
		$pageCollection = Mage::getResourceModel('cms/page_collection');
        $result = array(
            array('value' => '-1','label' => Mage::helper('flexibleblock')->__('All cms pages')),
            array('value' => '0','label' => Mage::helper('flexibleblock')->__('No page'))
        );

        foreach ($pageCollection as $page) {
            $result[] = array(
                'value' =>  $page->getPageId(),
                'label' =>  $page->getTitle()
            );
        }

        return$result;
	}
}