<?php 
class EM_Themeframework_Model_Config_Categorylevel
{
	public function toOptionArray()
    {
		return array(
			array('label' => "Disable", 'value' => "-1"),
			array('label' => "Show All Categories", 'value' => "0"),
			array('label' => "Level 1", 'value' => "1"),
			array('label' => "Level 2", 'value' => "2"),
			array('label' => "Level 3", 'value' => "3"),
		);
    }
}
