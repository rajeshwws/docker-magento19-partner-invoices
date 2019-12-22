<?php
  
class Goldenscent_Partners_Model_Mysql4_Partners_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('partners/partners');
    }
} 