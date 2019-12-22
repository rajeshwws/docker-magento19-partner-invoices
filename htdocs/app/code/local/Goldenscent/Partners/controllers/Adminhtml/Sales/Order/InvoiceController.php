<?php
require_once 'Mage/Adminhtml/controllers/Sales/Order/InvoiceController.php';
class Goldenscent_Partners_Adminhtml_Sales_Order_InvoiceController extends Mage_Adminhtml_Sales_Order_InvoiceController
{

    public function saveAction()
    {

        $data = $this->getRequest()->getPost('invoice');
        $orderId = $this->getRequest()->getParam('order_id');

        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

        try {
            $invoice = $this->_initInvoice();
            if ($invoice) {

                if (!empty($data['capture_case'])) {
                    $invoice->setRequestedCaptureCase($data['capture_case']);
                }

                if (!empty($data['comment_text'])) {
                    $invoice->addComment(
                        $data['comment_text'],
                        isset($data['comment_customer_notify']),
                        isset($data['is_visible_on_front'])
                    );
                }

                $invoices = $this->_initInvoiceGroups($invoice); 

                foreach ($invoices as $invoice) {

                    $invoice->register();

                    if (!empty($data['send_email'])) {
                        $invoice->setEmailSent(true);
                    }

                    $invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
                    $invoice->getOrder()->setIsInProcess(true);

                    $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder());
                    $shipment = false;
                    if (!empty($data['do_shipment']) || (int) $invoice->getOrder()->getForcedDoShipmentWithInvoice()) {
                        $shipment = $this->_prepareShipment($invoice);
                        if ($shipment) {
                            $shipment->setEmailSent($invoice->getEmailSent());
                            $transactionSave->addObject($shipment);
                        }
                    }
                    $transactionSave->save();
                }

                if (isset($shippingResponse) && $shippingResponse->hasErrors()) {
                    $this->_getSession()->addError($this->__('The invoice and the shipment  have been created. The shipping label cannot be created at the moment.'));
                } elseif (!empty($data['do_shipment'])) {
                    $this->_getSession()->addSuccess($this->__('The invoice and shipment have been created.'));
                } else {
                    $this->_getSession()->addSuccess($this->__('The invoice has been created.'));
                }

                // send invoice/shipment emails
                $comment = '';
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
                try {
                    $invoice->sendEmail(!empty($data['send_email']), $comment);
                } catch (Exception $e) {
                    Mage::logException($e);
                    $this->_getSession()->addError($this->__('Unable to send the invoice email.'));
                }
                if ($shipment) {
                    try {
                        $shipment->sendEmail(!empty($data['send_email']));
                    } catch (Exception $e) {
                        Mage::logException($e);
                        $this->_getSession()->addError($this->__('Unable to send the shipment email.'));
                    }
                }
                Mage::getSingleton('adminhtml/session')->getCommentText(true);
                $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
            } else {
                $this->_redirect('*/*/new', array('order_id' => $orderId));
            }
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Unable to save the invoice.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/new', array('order_id' => $orderId));

    }

    protected function _initInvoiceGroups($invoice)
    {
        $order = $invoice->getOrder();
        $qtys = $this->_getItemQtys();
        $shipmentGroups = array();

        if (!empty($order->getPartnerName())) {

            $allItems = array();

            foreach ($qtys as $itemId => $qty) {
                while ($qty--) $allItems[] = $itemId;
            }

            $partnerItems = array();
            $webShopItems = array();

            for ($i = 0; $i < count($allItems); $i++) { 
                if($i % 2 == 0) {
                    $webShopItems[] = $allItems[$i];
                } else {
                    $partnerItems[] = $allItems[$i];
                }
            }

            if (!empty($webShopItems)) {
                $groupItems = array_count_values($webShopItems);
                $shipmentGroups[] =  Mage::getModel('sales/service_order', $order)->prepareInvoice($groupItems);
            }

            if (!empty($partnerItems)) {
                $groupItems = array_count_values($partnerItems);
                $shipmentGroups[] =  Mage::getModel('sales/service_order', $order)->prepareInvoice($groupItems);
            }
        } else {
             $shipmentGroups[] =  Mage::getModel('sales/service_order', $order)->prepareInvoice($qtys);
        }

        return $shipmentGroups;
    }

}