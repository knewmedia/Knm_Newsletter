<?php
/**
 *
 * @category    Knm
 * @package     Knm_Newsletter
 * @copyright   Copyright (c) 2012 K - New Media GmbH & Co. KG
 * @link        http://www.k-newmedia.de/
 * @author      k-newmedia <info@k-newmedia.de>
 */

class Knm_Newsletter_Block_Adminhtml_Template_Edit extends Mage_Adminhtml_Block_Newsletter_Template_Edit
{

    /**
     * Preparing block layout
     *
     * @return Mage_Adminhtml_Block_Newsletter_Template_Edit
     */
    protected function _prepareLayout()
    {

        // Load Wysiwyg on demand and Prepare layout
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled() && ($block = $this->getLayout()->getBlock('head'))) {
            $block->setCanLoadTinyMce(true);
        }

        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label' => Mage::helper('newsletter')->__('Back'),
                'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                'class' => 'back'
            ))
        );

        $this->setChild('reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label' => Mage::helper('newsletter')->__('Reset'),
                'onclick' => 'window.location.href = window.location.href'
            ))
        );
        /*
                $this->setChild('to_plain_button',
                    $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label'     => Mage::helper('newsletter')->__('Convert to Plain Text'),
                            'onclick'   => 'templateControl.stripTags();',
                            'id'        => 'convert_button',
                            'class'     => 'task'
                        ))
                );
        */
        $this->setChild('to_html_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label' => Mage::helper('newsletter')->__('Return HTML Version'),
                'onclick' => 'templateControl.unStripTags();',
                'id' => 'convert_button_back',
                'style' => 'display:none',
                'class' => 'task'
            ))
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label' => Mage::helper('newsletter')->__('Save Template'),
                'onclick' => 'templateControl.save();',
                'class' => 'save'
            ))
        );

        $this->setChild('save_as_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label' => Mage::helper('newsletter')->__('Save As'),
                'onclick' => 'templateControl.saveAs();',
                'class' => 'save'
            ))
        );

        $this->setChild('preview_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label' => Mage::helper('newsletter')->__('Preview Text Template'),
                'onclick' => 'templateControl.preview();',
                'class' => 'task'
            ))
        );

        $this->setChild('preview_html_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label' => Mage::helper('newsletter')->__('Preview Html Template'),
                'onclick' => 'templateControl.htmlpreview();',
                'class' => 'task'
            ))
        );

        $this->setChild('preview_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label' => Mage::helper('newsletter')->__('Preview Text Template'),
                'onclick' => 'templateControl.preview();',
                'class' => 'task'
            ))
        );

        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label' => Mage::helper('newsletter')->__('Delete Template'),
                'onclick' => 'templateControl.deleteTemplate();',
                'class' => 'delete'
            ))
        );

        return $this;
    }

    /**
     * Retrieve Convert to HTML Button HTML
     *
     * @return string
     */
    public function getToHtmlButtonHtml()
    {
        return $this->getChildHtml('to_html_button');
    }

    /**
     * Retrieve Save Button HTML
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Retrieve Preview Button HTML
     *
     * @return string
     */
    public function getPreviewButtonHtml()
    {
        return $this->getChildHtml('preview_button');
    }

    /**
     * Retrieve Preview Button HTML
     *
     * @return string
     */
    public function getPreviewHtmlButtonHtml()
    {
        return $this->getChildHtml('preview_html_button');
    }

    /**
     * Retrieve Delete Button HTML
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Retrieve Save as Button HTML
     *
     * @return string
     */
    public function getSaveAsButtonHtml()
    {
        return $this->getChildHtml('save_as_button');
    }

    /**
     * Set edit flag for block
     *
     * @param boolean $value
     * @return Mage_Adminhtml_Block_Newsletter_Template_Edit
     */
    public function setEditMode($value = true)
    {
        $this->_editMode = (bool)$value;
        return $this;
    }

    /**
     * Return edit flag for block
     *
     * @return boolean
     */
    public function getEditMode()
    {
        return $this->_editMode;
    }

    /**
     * Return header text for form
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getEditMode()) {
            return Mage::helper('newsletter')->__('Edit Newsletter Template');
        }

        return Mage::helper('newsletter')->__('New Newsletter Template');
    }

    /**
     * Return form block HTML
     *
     * @return string
     */
    public function getForm()
    {
        /*
        return $this->getLayout()
            ->createBlock('adminhtml/newsletter_template_edit_form')
            ->toHtml();
        */
        $x = $this->getLayout();
        $x->createBlock('adminhtml/newsletter_template_edit_form');
        return $x->toHtml();
    }

    /**
     * Return return template name for JS
     *
     * @return string
     */
    public function getJsTemplateName()
    {
        return addcslashes($this->getModel()->getTemplateCode(), "\"\r\n\\");
    }

    /**
     * Return action url for form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }

    /**
     * Return preview action url for form
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/preview');
    }

    /**
     * Check Template Type is Plain Text
     *
     * @return bool
     */
    public function isTextType()
    {
        return $this->getModel()->isPlain();
    }

    /**
     * Return delete url for customer group
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('id' => $this->getRequest()->getParam('id')));
    }

    /**
     * Retrieve Save As Flag
     *
     * @return int
     */
    public function getSaveAsFlag()
    {
        return $this->getRequest()->getParam('_save_as_flag') ? '1' : '';
    }

    /**
     * Getter for single store mode check
     *
     * @return boolean
     */
    protected function isSingleStoreMode()
    {
        return Mage::app()->isSingleStoreMode();
    }

    /**
     * Getter for id of current store (the only one in single-store mode and current in multi-stores mode)
     *
     * @return boolean
     */
    protected function getStoreId()
    {
        return Mage::app()->getStore(true)->getId();
    }
}
