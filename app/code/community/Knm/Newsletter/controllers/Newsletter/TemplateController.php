<?php
/**
 *
 * @category    Knm
 * @package     Knm_Newsletter
 * @copyright   Copyright (c) 2012 K - New Media GmbH & Co. KG
 * @link        http://www.k-newmedia.de/
 * @author      k-newmedia <info@k-newmedia.de>
 */

require_once('Mage/Adminhtml/controllers/Newsletter/TemplateController.php');

class Knm_Newsletter_Newsletter_TemplateController extends Mage_Adminhtml_Newsletter_TemplateController
{
    /**
     * Save Newsletter Template
     */
    public function saveAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/newsletter_template'));
        }
        $template = Mage::getModel('newsletter/template');

        if ($id = (int)$request->getParam('id')) {
            $template->load($id);
        }

        try {
            $template->addData($request->getParams())
                ->setTemplateSubject($request->getParam('subject'))
                ->setTemplateCode($request->getParam('code'))
                ->setTemplateSenderEmail($request->getParam('sender_email'))
                ->setTemplateSenderName($request->getParam('sender_name'))
                ->setTemplateType($request->getParam('template_type')) //add additional select for Template-Type
                ->setTemplateText($request->getParam('text'))
                ->setTemplateHtml($request->getParam('html')) //add additional html field
                ->setTemplateStyles($request->getParam('styles'))
                ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate());

            if ($this->getRequest()->getParam('_change_type_flag')) {
                $template->setTemplateType(Mage_Newsletter_Model_Template::TYPE_TEXT);
                $template->setTemplateStyles('');
            }
            if ($this->getRequest()->getParam('_save_as_flag')) {
                $template->setId(null);
            }
            $template->save();
            $this->_redirect('*/*');
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(nl2br($e->getMessage()));
            $this->_getSession()->setData('newsletter_template_form_data',
                $this->getRequest()->getParams());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('An error occurred while saving this template.'));
            $this->_getSession()->setData('newsletter_template_form_data', $this->getRequest()->getParams());
        }
        $this->_forward('new');
    }

    /**
     * Preview Newsletter HTML template
     *
     */
    public function htmlpreviewAction()
    {
        $this->_setTitle();
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
}