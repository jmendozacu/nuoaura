<?php
 
class EM_Themeframework_Model_Config_Header_Currency
{
	public function toOptionArray()
    {
		return array(
			array('label' => "Style 01 (List Style)", 'value' => "list"),
			array('label' => "Style 02 (Hover Style)", 'value' => "hover"),
		);
    }
 
}
