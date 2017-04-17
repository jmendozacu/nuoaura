<?php
 
class EM_Themeframework_Model_Config_Absolute
{
	public function toOptionArray()
    {
		return array(
			array('label' => "Masonry", 'value' => "masonry"),
			array('label' => "Fit Rows", 'value' => "fitRows")
		);
    }
 
}
