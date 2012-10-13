<?php
/**
 * subscribe, unsubscribe and recommend for a newsletter
 *
 * @category    Knm
 * @package     Knm_Newsletter
 * @copyright   Copyright (c) 2012 K - New Media GmbH & Co. KG
 * @link        http://www.k-newmedia.de/
 * @author      k-newmedia <info@k-newmedia.de>
 */

class Knm_Newsletter_Block_Subscribe extends Mage_Newsletter_Block_Subscribe
{
    /**
     * form URL
     * @return string
     */
    public function getFormUrl()
    {
        return $this->getUrl('newsletter/subscriber');
    }

    /**
     * URL for subscribe
     * @return string
     */
    public function getNewActionUrl()
    {
        return $this->getUrl('newsletter/subscriber/new');
    }

    /**
     * URL for unsubscribing
     * @return string
     */
    public function getDeleteActionUrl()
    {
        return $this->getUrl('newsletter/subscriber/unsubscribe');
    }

}