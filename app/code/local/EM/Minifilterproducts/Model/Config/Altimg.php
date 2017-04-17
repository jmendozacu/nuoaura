<?php
class EM_Minifilterproducts_Model_Config_Altimg
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
		return array(
			array('label' => Mage::helper("minifilterproducts")->__("- None -") , 'value' => ""),
			array('label' => Mage::helper("minifilterproducts")->__("Base Image") , 'value' => "image"),
			array('label' => Mage::helper("minifilterproducts")->__("Small Image") , 'value' => "small_image"),
			array('label' => Mage::helper("minifilterproducts")->__("Thumbnail") , 'value' => "thumbnail")
		);
    }
}