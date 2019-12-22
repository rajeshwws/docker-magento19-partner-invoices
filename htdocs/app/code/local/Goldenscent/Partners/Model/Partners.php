<?php
  
class Goldenscent_Partners_Model_Partners extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('partners/partners');
    }
} 