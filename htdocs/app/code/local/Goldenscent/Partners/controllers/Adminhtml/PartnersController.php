<?php
  
class Goldenscent_Partners_Adminhtml_PartnersController extends Mage_Adminhtml_Controller_Action
{
  
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('partners/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }  
    
    public function indexAction() {
        $this->_initAction();      
        //$this->_addContent($this->getLayout()->createBlock('partners/adminhtml_partners'));
        $this->renderLayout();
    }
  
    public function editAction()
    {
        $partnersId     = $this->getRequest()->getParam('id');
        $partnersModel  = Mage::getModel('partners/partners')->load($partnersId);
  
        if ($partnersModel->getId() || $partnersId == 0) {
  
            Mage::register('partners_data', $partnersModel);
  
            $this->loadLayout();
            $this->_setActiveMenu('partners/items');
            
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
            
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            
            $this->_addContent($this->getLayout()->createBlock('partners/adminhtml_partners_edit'))
                 ->_addLeft($this->getLayout()->createBlock('partners/adminhtml_partners_edit_tabs'));
                
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('partners')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
            try {
                $postData = $this->getRequest()->getPost();
                $partnersModel = Mage::getModel('partners/partners');
                
                $partnersModel->setId($this->getRequest()->getParam('id'))
                    ->setTitle($postData['title'])
                    ->setCreatedTime(date('Y-m-d H:i:s'))
                    ->setUpdateTime(date('Y-m-d H:i:s'))
                    ->save();
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPartnersData(false);
  
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPartnersData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $partnersModel = Mage::getModel('partners/partners');
                
                $partnersModel->setId($this->getRequest()->getParam('id'))
                    ->delete();
                    
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
    /**
     * Product grid for AJAX request.
     * Sort and filter result for example.
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('partners/adminhtml_partners_grid')->toHtml()
        );
    }
} 