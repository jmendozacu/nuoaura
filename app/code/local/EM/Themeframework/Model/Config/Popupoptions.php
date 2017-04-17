<?php
 
class EM_Themeframework_Model_Config_Popupoptions
{
	public function toOptionArray()
    {
		return array(
			array('label' => "- None -", 'value' => "0"),
			array('label' => "Login Form", 'value' => "1"),			
			array('label' => "Newsletter", 'value' => "2"),			
			array('label' => "Flexible Block", 'value' => "3"),			
		);
    }
 
}
