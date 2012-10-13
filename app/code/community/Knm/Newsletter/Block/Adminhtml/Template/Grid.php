<?php
/**
 *
 * @category    Knm
 * @package     Knm_Newsletter
 * @copyright   Copyright (c) 2012 K - New Media GmbH & Co. KG
 * @link        http://www.k-newmedia.de/
 * @author      k-newmedia <info@k-newmedia.de>
 */

class Knm_Newsletter_Block_Adminhtml_Template_Grid extends Mage_Adminhtml_Block_Newsletter_Template_Grid
{

    protected function _prepareColumns()
    {
        $this->addColumn('template_code',
            array('header' => Mage::helper('newsletter')->__('ID'), 'align' => 'center', 'index' => 'template_id'));
        $this->addColumn('code',
            array(
                'header' => Mage::helper('newsletter')->__('Template Name'),
                'index' => 'template_code'
            ));

        $this->addColumn('added_at',
            array(
                'header' => Mage::helper('newsletter')->__('Date Added'),
                'index' => 'added_at',
                'gmtoffset' => true,
                'type' => 'datetime'
            ));

        $this->addColumn('modified_at',
            array(
                'header' => Mage::helper('newsletter')->__('Date Updated'),
                'index' => 'modified_at',
                'gmtoffset' => true,
                'type' => 'datetime'
            ));

        $this->addColumn('subject',
            array(
                'header' => Mage::helper('newsletter')->__('Subject'),
                'index' => 'template_subject'
            ));

        $this->addColumn('sender',
            array(
                'header' => Mage::helper('newsletter')->__('Sender'),
                'index' => 'template_sender_email',
                'renderer' => 'adminhtml/newsletter_template_grid_renderer_sender'
            ));

        $this->addColumn('type',
            array(
                'header' => Mage::helper('newsletter')->__('Template Type'),
                'index' => 'template_type',
                'type' => 'options',
                'options' => array(
                    Knm_Newsletter_Model_Template::TYPE_HTML => 'html',
                    Knm_Newsletter_Model_Template::TYPE_TEXT => 'text',
                    Knm_Newsletter_Model_Template::TYPE_MULTIPART => 'multipart'
                ),
            ));

        $this->addColumn('action',
            array(
                'header' => Mage::helper('newsletter')->__('Action'),
                'index' => 'template_id',
                'sortable' => false,
                'filter' => false,
                'no_link' => true,
                'width' => '170px',
                'renderer' => 'adminhtml/newsletter_template_grid_renderer_action'
            ));

        return $this;
    }
}