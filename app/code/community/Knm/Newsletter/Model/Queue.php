<?php
/**
 *
 * @category    Knm
 * @package     Knm_Newsletter
 * @copyright   Copyright (c) 2012 K - New Media GmbH & Co. KG
 * @link        http://www.k-newmedia.de/
 * @author      k-newmedia <info@k-newmedia.de>
 */

class Knm_Newsletter_Model_Queue extends Mage_Newsletter_Model_Queue
{

    /**
     * Send messages to subscribers for this queue
     *
     * @param   int     $count
     * @param   array   $additionalVariables
     * @return  Mage_Newsletter_Model_Queue
     */

    public function sendPerSubscriber($count = 20, array $additionalVariables = array())
    {
        if ($this->getQueueStatus() != self::STATUS_SENDING && ($this->getQueueStatus() != self::STATUS_NEVER && $this->getQueueStartAt())) {
            return $this;
        }

        if ($this->getSubscribersCollection()->getSize() == 0) {
            return $this;
        }

        $collection = $this->getSubscribersCollection()
            ->useOnlyUnsent()
            ->showCustomerInfo()
            ->setPageSize($count)
            ->setCurPage(1)
            ->load();

        $newslettertext = $this->getNewsletterText();
        $newsletterhtml = $this->getNewsletterHtml();

        /* @var $sender Knm_Newsletter_Model_Email_Template */
        $sender = Mage::getModel('knm_newsletter/email_template');
        $sender->setSenderName($this->getNewsletterSenderName())
            ->setSenderEmail($this->getNewsletterSenderEmail())
            ->setTemplateType($this->getNewsletterType())
            ->setTemplateSubject($this->getNewsletterSubject())
            ->setTemplateText($newslettertext)
            ->setTemplateHtml($newsletterhtml)
            ->setTemplateStyles($this->getNewsletterStyles())
            ->setTemplateFilter(Mage::helper('newsletter')->getTemplateProcessor());

        foreach ($collection->getItems() as $item) {
            $email = $item->getSubscriberEmail();
            $name = $item->getSubscriberFullName();

            $sender->emulateDesign($item->getStoreId());
            $successSend = $sender->send($email, $name, array('subscriber' => $item, 'newsletter_id' => $this->_getNewsletterId()));
            $sender->revertDesign();

            if ($successSend) {
                $item->received($this);
            } else {
                $problem = Mage::getModel('newsletter/problem');
//TODO: fix undefined var $queue
                $problem->addSubscriberData($item)
                    ->addQueueData($queue)
                    ->addErrorData('Please refer to exeption.log')
                    ->save();

                $item->received($this);
            }
        }

        if (count($collection->getItems()) < $count - 1 || count($collection->getItems()) == 0) {
            $this->setQueueFinishAt(now());
            $this->setQueueStatus(self::STATUS_SENT);
            $this->save();
        }
        return $this;
    }

    /**
     * Returns the newsletter id in format DOB_KWXX_YYYMMDD, where YYYYMMDD is
     * the current date and XX is the current KW.
     *
     * @return string - the actial newsletter id
     */
    protected function _getNewsletterId()
    {
        $date = new Zend_Date($this->getQueueStartAt(), 'yyyy-MM-dd HH:mm:ss');
        return 'DOB_KW' . $date->toString('ww_yyyyMMdd');
    }

}

