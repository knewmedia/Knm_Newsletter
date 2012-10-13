<?php
/**
 *
 * @category    Knm
 * @package     Knm_Newsletter
 * @copyright   Copyright (c) 2012 K - New Media GmbH & Co. KG
 * @link        http://www.k-newmedia.de/
 * @author      k-newmedia <info@k-newmedia.de>
 */

class Knm_Newsletter_Block_Adminhtml_Template_Edit_Form extends Mage_Adminhtml_Block_Newsletter_Template_Edit_Form
{
    /**
     * Define Form settings
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Newsletter_Template_Edit_Form
     */
    protected function _prepareForm()
    {

        $model = $this->getModel();
        $identity = Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY);
        $identityName = Mage::getStoreConfig('trans_email/ident_' . $identity . '/name');
        $identityEmail = Mage::getStoreConfig('trans_email/ident_' . $identity . '/email');

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('newsletter')->__('Template Information'),
            'class' => 'fieldset-wide'
        ));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name' => 'id',
                'value' => $model->getId(),
            ));
        }

        $fieldset->addField('code', 'text', array(
            'name' => 'code',
            'label' => Mage::helper('newsletter')->__('Template Name'),
            'title' => Mage::helper('newsletter')->__('Template Name'),
            'required' => true,
            'value' => $model->getTemplateCode(),
        ));

        $fieldset->addField('subject', 'text', array(
            'name' => 'subject',
            'label' => Mage::helper('newsletter')->__('Template Subject'),
            'title' => Mage::helper('newsletter')->__('Template Subject'),
            'required' => true,
            'value' => $model->getTemplateSubject(),
        ));

        $fieldset->addField('sender_name', 'text', array(
            'name' => 'sender_name',
            'label' => Mage::helper('newsletter')->__('Sender Name'),
            'title' => Mage::helper('newsletter')->__('Sender Name'),
            'required' => true,
            'value' => $model->getId() !== null
                ? $model->getTemplateSenderName()
                : $identityName,
        ));

        $fieldset->addField('sender_email', 'text', array(
            'name' => 'sender_email',
            'label' => Mage::helper('newsletter')->__('Sender Email'),
            'title' => Mage::helper('newsletter')->__('Sender Email'),
            'class' => 'validate-email',
            'required' => true,
            'value' => $model->getId() !== null
                ? $model->getTemplateSenderEmail()
                : $identityEmail
        ));

        $fieldset->addField('template_type', 'select', array(
            'name' => 'template_type',
            'label' => Mage::helper('newsletter')->__('Template Type'),
            'required' => true,
            'value' => $model->getTemplateType(),
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

        //Input field Text Version
        $fieldset->addField('text', 'textarea', array(
            'name' => 'text',
            'label' => Mage::helper('newsletter')->__('Template Content Text'),
            'title' => Mage::helper('newsletter')->__('Template Content Text'),
            'style' => 'height:36em;',
            'value' => $model->getTemplateText()
        ));

        $widgetFilters = array('is_email_compatible' => 1);
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('widget_filters' => $widgetFilters));
        if ($model->isPlain()) {
            $wysiwygConfig->setEnabled(false);
        }
        //Input field HTML Version
        $fieldset->addField('html', 'editor', array(
            'name' => 'html',
            'label' => Mage::helper('newsletter')->__('Template Content HTML'),
            'title' => Mage::helper('newsletter')->__('Template Content HTML'),
            'required' => true,
            'state' => 'html',
            'style' => 'height:36em;',
            'value' => $model->getTemplateHtml(),
            'config' => $wysiwygConfig
        ));

        if (!$model->isPlain()) {
            $fieldset->addField('template_styles', 'textarea', array(
                'name' => 'styles',
                'label' => Mage::helper('newsletter')->__('Template Styles'),
                'container_id' => 'field_template_styles',
                'value' => $model->getTemplateStyles()
            ));
        }

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return $this;
    }
}
