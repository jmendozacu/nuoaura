<?php
class EM_Megamenupro_Block_Adminhtml_Renderer_Description extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
	 /**
     * Render a grid cell as options
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
		$num = 100;
		if(strlen($value) > $num){
			$value = substr($value,0,$num);
			$plit = explode(" ",$value);
			$count = count($plit);
			unset($plit[$count-1]);
			$value = implode(" ",$plit)." ...";
		}
		return $value;
    }
}
?>