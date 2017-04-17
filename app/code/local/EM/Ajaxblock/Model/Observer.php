<?php
class EM_Ajaxblock_Model_Observer
{
	public function htmlBefore(Varien_Event_Observer $observer)
	{
	    $block = $observer->getEvent()->getBlock();
	    if($block->getData('ajaxblock'))
	    {
	        return;
	    }
	    else
	    {
	        $block->setData('ajaxblock',true);
            if(get_class($block) == 'EM_Tabs_Block_Group')
            {
                $data = base64_encode(json_encode($block->getData()));
                $block->setData('data',$data);
                $block->setTemplate('ajaxblock/index.phtml');
            }
            if(get_class($block) == 'EM_Filterproducts_Block_List')
            {
                $data = base64_encode(json_encode($block->getData()));
                $block->setData('data',$data);
                $block->setData('choose_template','ajaxblock/index_filter.phtml');
            }
            
            if(get_class($block) == 'EM_Minifilterproducts_Block_List')
            {
                $data = base64_encode(json_encode($block->getData()));
                $block->setData('data',$data);
                $block->setData('custom_theme','ajaxblock/index_filter.phtml');
            }
        }
	}
		
}
