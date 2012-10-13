<?php
/**
 *
 * @category    Knm
 * @package     Knm_Newsletter
 * @copyright   Copyright (c) 2012 K - New Media GmbH & Co. KG
 * @link        http://www.k-newmedia.de/
 * @author      k-newmedia <info@k-newmedia.de>
 */

require_once('Mage/Adminhtml/controllers/Newsletter/QueueController.php');

class Knm_Newsletter_Newsletter_QueueController extends Mage_Adminhtml_Newsletter_QueueController
{

    /**
     * Preview Newsletter queue template
     */
    public function previewAction()
    {
        $this->loadLayout();
        $data = $this->getRequest()->getParams();
        if (empty($data) || !isset($data['id'])) {
            $this->_forward('noRoute');
            return $this;
        }

        // set default value for selected store
        $data['preview_store_id'] = Mage::app()->getDefaultStoreView()->getId();

        $this->getLayout()->getBlock('preview_form')->setFormData($data);
        $this->renderLayout();
    }

    public function sendingAction()
    {
        // Todo: put it somewhere in config!
        $countOfQueue = Mage::getStoreConfig('newsletter/settings/count_of_queue') ? Mage::getStoreConfig('newsletter/settings/count_of_queue') : 3;
        $countOfSubscritions = Mage::getStoreConfig('newsletter/settings/count_of_subscriptions') ? Mage::getStoreConfig('newsletter/settings/count_of_subscriptions') : 20;

        $collection = Mage::getResourceModel('newsletter/queue_collection')
            ->setPageSize($countOfQueue)
            ->setCurPage(1)
            ->addOnlyForSendingFilter()
            ->load();

        $collection->walk('sendPerSubscriber', array($countOfSubscritions));
    }

    /**
     * Save Queue action
     */
    public function saveAction()
    {
        try {
            /* @var $queue Mage_Newsletter_Model_Queue */
            $queue = Mage::getModel('newsletter/queue');

            $templateId = $this->getRequest()->getParam('template_id');
            if ($templateId) {
                /* @var $template Mage_Newsletter_Model_Template */
                $template = Mage::getModel('newsletter/template')->load($templateId);

                if (!$template->getId() || $template->getIsSystem()) {
                    Mage::throwException($this->__('Wrong newsletter template.'));
                }

                $queue->setTemplateId($template->getId())
                    ->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_NEVER);
            } else {
                $queue->load($this->getRequest()->getParam('id'));
            }

            if (!in_array($queue->getQueueStatus(),
                array(Mage_Newsletter_Model_Queue::STATUS_NEVER,
                    Mage_Newsletter_Model_Queue::STATUS_PAUSE))
            ) {
                $this->_redirect('*/*');
                return;
            }

            if ($queue->getQueueStatus() == Mage_Newsletter_Model_Queue::STATUS_NEVER) {
                $queue->setQueueStartAtByString($this->getRequest()->getParam('start_at'));
            }

            //$a = $this->getRequest()->getParam('html_message');
            //$b = $this->getRequest()->getParam('template_type');

            $queue->setStores($this->getRequest()->getParam('stores', array()))
                ->setNewsletterSubject($this->getRequest()->getParam('subject'))
                ->setNewsletterSenderName($this->getRequest()->getParam('sender_name'))
                ->setNewsletterSenderEmail($this->getRequest()->getParam('sender_email'))
                ->setNewsletterType($this->getRequest()->getParam('template_type')) //add additional select for Template-Type
                ->setNewsletterText($this->getRequest()->getParam('text'))
                ->setNewsletterHtml($this->getRequest()->getParam('html_message')) //add additional html field
                ->setNewsletterStyles($this->getRequest()->getParam('styles'));

            if ($queue->getQueueStatus() == Mage_Newsletter_Model_Queue::STATUS_PAUSE
                && $this->getRequest()->getParam('_resume', false)
            ) {
                $queue->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_SENDING);
            }

            $queue->save();
            $this->_redirect('*/*');
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $this->_redirect('*/*/edit', array('id' => $id));
            } else {
                $this->_redirectReferer();
            }
        }
    }

}
