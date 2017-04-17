<?php
 
class EM_Themeframework_Model_Config_Blockreplace
{
	public function toOptionArray()
    {
		return array(
			array('label' => "Disable Completely", 'value' => "disable"),
			array('label' => "Don't Replace With Flexible Block", 'value' => "dont_replace"),
			array('label' => "If Empty, Replace With Flexible Block", 'value' => "replace_empty"),
			array('label' => "Replace With Flexible Block", 'value' => "replace"),			
		);
    } 
}
