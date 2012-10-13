<?php
/**
 *
 * @category    Knm
 * @package     Knm_Newsletter
 * @copyright   Copyright (c) 2012 K - New Media GmbH & Co. KG
 * @link        http://www.k-newmedia.de/
 * @author      k-newmedia <info@k-newmedia.de>
 */

class Knm_Newsletter_Block_Adminhtml_Queue_Edit_Form extends Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form
{
    /**
     * Prepare form for newsletter queue editing.
     * Form can be run from newsletter template grid by option "Queue newsletter"
     * or from  newsletter queue grid by edit option.
     *
     * @param void
     * @return Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form
     */
    protected function _prepareForm()
    {
        /* @var $queue Mage_Newsletter_Model_Queue */
        $queue = Mage::getSingleton('newsletter/queue');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('newsletter')->__('Queue Information')
        ));

        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        if ($queue->getQueueStatus() == Mage_Newsletter_Model_Queue::STATUS_NEVER) {
            $fieldset->addField('date', 'date', array(
                'name' => 'start_at',
                'time' => true,
                'format' => $outputFormat,
                'label' => Mage::helper('newsletter')->__('Queue Date Start'),
                'image' => $this->getSkinUrl('images/grid-cal.gif')
            ));

            if (!Mage::app()->isSingleStoreMode()) {
                $fieldset->addField('stores', 'multiselect', array(
                    'name' => 'stores[]',
                    'label' => Mage::helper('newsletter')->__('Subscribers From'),
                    'image' => $this->getSkinUrl('images/grid-cal.gif'),
                    'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
                    'value' => $queue->getStores()
                ));
            } else {
                $fieldset->addField('stores', 'hidden', array(
                    'name' => 'stores[]',
                    'value' => Mage::app()->getStore(true)->getId()
                ));
            }
        } else {
            $fieldset->addField('date', 'date', array(
                'name' => 'start_at',
                'time' => true,
                'disabled' => 'true',
                'style' => 'width:38%;',
                'format' => $outputFormat,
                'label' => Mage::helper('newsletter')->__('Queue Date Start'),
                'image' => $this->getSkinUrl('images/grid-cal.gif')
            ));

            if (!Mage::app()->isSingleStoreMode()) {
                $fieldset->addField('stores', 'multiselect', array(
                    'name' => 'stores[]',
                    'label' => Mage::helper('newsletter')->__('Subscribers From'),
                    'image' => $this->getSkinUrl('images/grid-cal.gif'),
                    'required' => true,
                    'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
                    'value' => $queue->getStores()
                ));
            } else {
                $fieldset->addField('stores', 'hidden', array(
                    'name' => 'stores[]',
                    'value' => Mage::app()->getStore(true)->getId()
                ));
            }
        }

        if ($queue->getQueueStartAt()) {
            $form->getElement('date')->setValue(
                Mage::app()->getLocale()->date($queue->getQueueStartAt(), Varien_Date::DATETIME_INTERNAL_FORMAT)
            );
        }

        $fieldset->addField('subject', 'text', array(
            'name' => 'subject',
            'label' => Mage::helper('newsletter')->__('Subject'),
            'required' => true,
            'value' => ($queue->isNew() ? $queue->getTemplate()->getTemplateSubject() : $queue->getNewsletterSubject())
        ));

        $fieldset->addField('sender_name', 'text', array(
            'name' => 'sender_name',
            'label' => Mage::helper('newsletter')->__('Sender Name'),
            'title' => Mage::helper('newsletter')->__('Sender Name'),
            'required' => true,
            'value' => ($queue->isNew() ? $queue->getTemplate()->getTemplateSenderName() : $queue->getNewsletterSenderName())
        ));

        $fieldset->addField('sender_email', 'text', array(
            'name' => 'sender_email',
            'label' => Mage::helper('newsletter')->__('Sender Email'),
            'title' => Mage::helper('newsletter')->__('Sender Email'),
            'class' => 'validate-email',
            'required' => true,
            'value' => ($queue->isNew() ? $queue->getTemplate()->getTemplateSenderEmail() : $queue->getNewsletterSenderEmail())
        ));

        $widgetFilters = array('is_email_compatible' => 1);
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('widget_filters' => $widgetFilters));

        //when saving for the first time
        if ($queue->isNew()) {
            $fieldset->addField('template_type', 'select', array(
                'name' => 'template_type',
                'label' => Mage::helper('newsletter')->__('Template Type'),
                'required' => true,
                'value' => $queue->getTemplate()->getTemplateType(),
                'values' => array(
                    array(
                        'value' => Knm_Newsletter_Model_Template::TYPE_TEXT,
                        'label' => Mage::helper('newsletter')->__('Plain'),
                    ),
                    array(
                        'value' => Knm_Newsletter_Model_Template::TYPE_HTML,
                        'label' => Mage::helper('newsletter')->__('HTML'),
                    ),
                    array(
                        'value' => Knm_Newsletter_Model_Template::TYPE_MULTIPART,
                        'label' => Mage::helper('newsletter')->__('Multipart'),
                    )
                )
            ));

            $fieldset->addField('text', 'editor', array(
                'name' => 'text',
                'label' => Mage::helper('newsletter')->__('Content Text'),
                'state' => 'html',
                'required' => true,
                'value' => $queue->getTemplate()->getTemplateText(),
                'style' => 'width:98%; height: 300px;',
                'config' => $wysiwygConfig
            ));

            $fieldset->addField('html_message', 'editor', array(
                'name' => 'html_message',
                'label' => Mage::helper('newsletter')->__('Content HTML'),
                'state' => 'html',
                'value' => $queue->getTemplate()->getTemplateHtml(),
                'style' => 'width:98%; height: 300px;',
                'config' => $wysiwygConfig
            ));

            $fieldset->addField('styles', 'textarea', array(
                'name' => 'styles',
                'label' => Mage::helper('newsletter')->__('Newsletter Styles'),
                'container_id' => 'field_newsletter_styles',
                'value' => $queue->getTemplate()->getTemplateStyles(),
                'style' => 'width:98%; height: 100px;'
            ));
            //already sent
        } elseif (Mage_Newsletter_Model_Queue::STATUS_NEVER != $queue->getQueueStatus()) {
            $fieldset->addField('text', 'textarea', array(
                'name' => 'text',
                'label' => Mage::helper('newsletter')->__('Text Message'),
                'value' => $queue->getNewsletterText(),
                'style' => 'width:98%; height: 300px;'
            ));

            $fieldset->addField('html_message', 'textarea', array(
                'name' => 'html_message',
                'label' => Mage::helper('newsletter')->__('HTML Message'),
                'value' => $queue->getNewsletterHtml(),
                'style' => 'width:98%; height: 300px;'
            ));

            $fieldset->addField('styles', 'textarea', array(
                'name' => 'styles',
                'label' => Mage::helper('newsletter')->__('Newsletter Styles'),
                'value' => $queue->getNewsletterStyles(),
                'style' => 'width:98%; height: 100px;'
            ));

            $form->getElement('text')->setDisabled('true')->setRequired(false);
            $form->getElement('styles')->setDisabled('true')->setRequired(false);
            $form->getElement('subject')->setDisabled('true')->setRequired(false);
            $form->getElement('sender_name')->setDisabled('true')->setRequired(false);
            $form->getElement('sender_email')->setDisabled('true')->setRequired(false);
            $form->getElement('stores')->setDisabled('true');
            //already saved but not sent yet
        } else {
            $fieldset->addField('template_type', 'select', array(
                'name' => 'template_type',
                'label' => Mage::helper('newsletter')->__('Template Type'),
                'required' => true,
                'value' => $queue->getTemplate()->getTemplateType(),
                'values' => array(
                    array(
                        'value' => Knm_Newsletter_Model_Template::TYPE_TEXT,
                        'label' => Mage::helper('newsletter')->__('Plain'),
                    ),
                    array(
                        'value' => Knm_Newsletter_Model_Template::TYPE_HTML,
                        'label' => Mage::helper('newsletter')->__('HTML'),
                    ),
                    array(
                        'value' => Knm_Newsletter_Model_Template::TYPE_MULTIPART,
                        'label' => Mage::helper('newsletter')->__('Multipart'),
                    )
                )
            ));

            $fieldset->addField('text', 'editor', array(
                'name' => 'text',
                'label' => Mage::helper('newsletter')->__('Text Message'),
                'state' => 'html',
                'required' => true,
                'value' => $queue->getNewsletterText(),
                'style' => 'width:98%; height: 300px;',
                'config' => $wysiwygConfig
            ));
            $fieldset->addField('html_message', 'editor', array(
                'name' => 'html_message',
                'label' => Mage::helper('newsletter')->__('HTML Message'),
                'state' => 'html',
                'value' => $queue->getNewsletterHtml(),
                'style' => 'width:98%; height: 300px;',
                'config' => $wysiwygConfig
            ));

            $fieldset->addField('styles', 'textarea', array(
                'name' => 'styles',
                'label' => Mage::helper('newsletter')->__('Newsletter Styles'),
                'value' => $queue->getNewsletterStyles(),
                'style' => 'width:98%; height: 100px;',
            ));
        }

        $this->setForm($form);
        return $this;
    }
}
