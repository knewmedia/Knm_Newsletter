<?php
/**
 * Load subscriber data for account newsletter block
 *
 * @category    Knm
 * @package     Knm_Newsletter
 * @copyright   Copyright (c) 2012 K - New Media GmbH & Co. KG
 * @link        http://www.k-newmedia.de/
 * @author      k-newmedia <info@k-newmedia.de>
 */

class Knm_Newsletter_Block_Account extends Mage_Newsletter_Block_Subscribe
{

    public function getSubscriber()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $subscriber = Mage::getModel('newsletter/subscriber')->loadByCustomer($customer);
        return $subscriber;
    }

    public function getActionUrl()
    {
        return $this->getUrl('newsletter/subscriber/save');
    }

}