<?php
  
class Goldenscent_Partners_Model_Mysql4_Partners extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {  
        $this->_init('partners/partners', 'partners_id');
    }
} 