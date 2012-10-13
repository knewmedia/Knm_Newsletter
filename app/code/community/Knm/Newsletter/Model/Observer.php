<?php
/**
 *
 * @category    Knm
 * @package     Knm_Newsletter
 * @copyright   Copyright (c) 2012 K - New Media GmbH & Co. KG
 * @link        http://www.k-newmedia.de/
 * @author      k-newmedia <info@k-newmedia.de>
 */

class Knm_Newsletter_Model_Observer extends Mage_Newsletter_Model_Observer
{

    public function scheduledSend($schedule)
    {
        $countOfQueue = Mage::getStoreConfig('newsletter/settings/count_of_queue') ? Mage::getStoreConfig('newsletter/settings/count_of_queue') : 3;
        $countOfSubscritions = Mage::getStoreConfig('newsletter/settings/count_of_subscriptions') ? Mage::getStoreConfig('newsletter/settings/count_of_subscriptions') : 20;

        $collection = Mage::getModel('newsletter/queue')->getCollection()
            ->setPageSize($countOfQueue)
            ->setCurPage(1)
            ->addOnlyForSendingFilter()
            ->load();

        $collection->walk('sendPerSubscriber', array($countOfSubscritions));
    }

}