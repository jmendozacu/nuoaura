<?php
 
class EM_Themeframework_Model_Config_Ratio
{
	public function toOptionArray()
    {
		return array(
			array('label' => "- None -", 'value' => 0),
			array('label' => "2 : 3", 'value' => 0.6666),
			array('label' => "3 : 4", 'value' => 0.75),
			array('label' => "1 : 1", 'value' => 1),
			array('label' => "4 : 3", 'value' => 1.3333),
			array('label' => "3 : 2", 'value' => 1.5),
            array('label' => "Custom Image Aspect Ratio", 'value' => -1),
		);
    }
 
}
