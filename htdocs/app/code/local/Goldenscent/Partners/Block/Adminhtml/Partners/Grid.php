<?php
  
class Goldenscent_Partners_Block_Adminhtml_Partners_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('partnersGrid');
        // This is the primary key of the database
        $this->setDefaultSort('partners_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
  
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('partners/partners')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
  
    protected function _prepareColumns()
    {
        $this->addColumn('partners_id', array(
            'header'    => Mage::helper('partners')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'partners_id',
        ));
  
        $this->addColumn('title', array(
            'header'    => Mage::helper('partners')->__('Title'),
            'align'     =>'left',
            'index'     => 'title',
        ));

        $this->addColumn('created_time', array(
            'header'    => Mage::helper('partners')->__('Creation Time'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'created_time',
        ));
  
        $this->addColumn('update_time', array(
            'header'    => Mage::helper('partners')->__('Update Time'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'update_time',
        ));    
  
        return parent::_prepareColumns();
    }
  
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
  
    public function getGridUrl()
    {
      return $this->getUrl('*/*/grid', array('_current'=>true));
    }
  
  
} 