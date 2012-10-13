<?php
/**
 *
 * @category    Knm
 * @package     Knm_Newsletter
 * @copyright   Copyright (c) 2012 K - New Media GmbH & Co. KG
 * @link        http://www.k-newmedia.de/
 * @author      k-newmedia <info@k-newmedia.de>
 */

require_once 'Mage/Newsletter/controllers/SubscriberController.php';

class Knm_Newsletter_SubscriberController extends Mage_Newsletter_SubscriberController
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction()
    {
        if ($this->getRequest()->isPost()) {

            $session = Mage::getSingleton('core/session');
            $newCustomer = $this->getRequest()->getPost('newsletter_subscribe');
            $email = (string)$newCustomer['email'];
            $prefix = (string)$newCustomer['prefix'];
            $firstname = (string)$newCustomer['firstname'];
            $lastname = (string)$newCustomer['lastname'];

            try {
                $emailValidator = new Zend_Validate_EmailAddress();
                if (!$emailValidator->isValid($email)) {
                    $e = '';
                    foreach ($emailValidator->getMessages() as $message) {
                        $e .= $message . '\n';
                    }
                    Mage::throwException($this->__('Bitte überprüfen Sie die eingegebene E-Mail Adresse: ' . $e));
                }

                $textValidator = new Zend_Validate_Alpha();
                if (!$textValidator->isValid($lastname) || !$textValidator->isValid($firstname)) {
                    Mage::throwException($this->__('Bitte überprüfen Sie den eingegebenen Vor- und Nachnamen.'));
                }
                if (!$textValidator->isValid($prefix)) {
                    Mage::throwException($this->__('Bitte überprüfen Sie die angegebene Ansprachen.'));
                }
                $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
                $subscriber->setPrefix($newCustomer['prefix']);
                $subscriber->setFirstname($newCustomer['firstname']);
                $subscriber->setLastname($newCustomer['lastname']);
                //$subscriber->setDob($newCustomer['year'] . '-' . $newCustomer['month'] . '-' . $newCustomer['day']); // . ' 00:00:00');
                $subscriber->setDob('1970-01-01');

                //$status = $subscriber->subscribe($email);

                if (count($session->getMessages()->getErrors()) == 0) {
                    $session->addSuccess($this->__('In Kürze erhalten Sie eine E-Mail. Um den dobramoda.pl Newsletter zu aktivieren klicken Sie bitte auf den darin enthaltenen Bestätigungslink!'));
                    return $this->_redirect('/');
                }

            } catch (Exception $e) {
                $session->addException($e, $this->__('Leider konnten wir Ihre Daten nicht erfassen: ' . $e->getMessage()));
            }
        }
        $this->_redirectReferer();
    }

    public function unsubscribeAction()
    {
        $email = (string)$this->getRequest()->getParam('email');

        if ($this->getRequest()->isPost()) {
            $session = Mage::getSingleton('core/session');
            try {
                $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
                if ($subscriber->getId()) {
                    $subscriber->unsubscribe();
                }
                $session->addSuccess($this->__('Sie haben sich erfolgreich abgemeldet.'));
            } catch (Mage_Core_Exception $e) {
                $session->addException($e, $e->getMessage());
            } catch (Exception $e) {
                $session->addException($e, $this->__('Bei der Abmeldung trat ein Fehler auf, bitte versuchen Sie es erneut!'));
            }
        }
        $this->_redirect('/');
    }

    /**
     * Callback action for https://www.xcampaign.ch/dispatcher/service
     */
    public function updateAction()
    {
        //$email = (string)$this->getRequest()->getParam('sys_email');
        $message = (string)$this->getRequest()->getParam('message');

        if ($message == 'success') {
            Mage::getSingleton('core/session')->addSuccess($this->__('Successfully sent mail')); /*Successfully sent mail*/
        } else {
            Mage::getSingleton('core/session')->addError((string)$this->getRequest()->getParam('error'));
        }
        $this->_redirect('*/*', array('_fragment' => 'update'));
    }

    /**
     * recommend Action to send recommendation
     */
    public function recommendAction()
    {
        if ($this->getRequest()->isPost()) {

            //simple spam protection
            //if the hidden text field has content it is spam
            try {
                if (strlen($this->getRequest()->getParam('newsletter_subscribe_val'))) {
                    Mage::throwException($this->__('Zugriffsversuch durch Spambot'));
                }
            } catch (Exception $e) {
                return $this->_redirect('/');
            }

            $session = Mage::getSingleton('core/session');
            //@var $subscriber Knm_Newsletter_Model_Subscriber
            $subscriber = Mage::getModel('newsletter/subscriber');

            $data = $this->getRequest()->getParam('newsletter_recommendation');
            $recommendation['recommender_firstname'] = (string)$data['firstname'];
            $recommendation['recommender_lastname'] = (string)$data['lastname'];

            $data = $this->getRequest()->getParam('newsletter_subscribe');
            $recommendation['prefix'] = (string)$data['prefix'];
            $recommendation['firstname'] = (string)$data['firstname'];
            $recommendation['lastname'] = (string)$data['lastname'];
            $recommendation['email'] = (string)$data['email'];
            $recommendation['message'] = filter_var((string)$data['message'], FILTER_SANITIZE_STRING);

            try {
                //validate email
                $emailValidator = new Zend_Validate_EmailAddress();
                if (!$emailValidator->isValid($recommendation['email'])) {
                    $e = '';
                    foreach ($emailValidator->getMessages() as $message) {
                        $e .= $message . '\n';
                    }
                    Mage::throwException($this->__('Bitte überprüfen Sie die eingegebene E-Mail Adresse: ' . $e));
                }

                //validate text fields
                $textValidator = new Zend_Validate_Alpha(array('allowWhiteSpace' => true));
                if (!$textValidator->isValid($recommendation['recommender_firstname']) || !$textValidator->isValid($recommendation['recommender_lastname'])) {
                    Mage::throwException($this->__('Bitte überprüfen Sie den eingegebenen Vor- und Nachnamen.'));
                }
                if (!$textValidator->isValid($recommendation['prefix'])) {
                    Mage::throwException($this->__('Bitte überprüfen Sie die angegebene Anrede.'));
                }
                if (!$textValidator->isValid($recommendation['firstname']) || !$textValidator->isValid($recommendation['lastname'])) {
                    Mage::throwException($this->__('Bitte überprüfen Sie den eingegebenen Vor- und Nachnamen.'));
                }

                //validate textarea
                $messageValidator = new Zend_Validate_StringLength(array('min' => 1, 'max' => 2000));
                if (!$messageValidator->isValid($recommendation['message'])) {
                    Mage::throwException($this->__('Bitte überprüfen Sie die Nachricht an den Empfänger.'));
                }

                $subscriber->loadByEmail($recommendation['email']);
                $subscriber->setPrefix($recommendation['prefix']);
                $subscriber->setFirstname($recommendation['firstname']);
                $subscriber->setLastname($recommendation['lastname']);
                $subscriber->setDob('1970-01-01');
                $subscriber->setRecommenderFirstname($recommendation['recommender_firstname']);
                $subscriber->setRecommenderLastname($recommendation['recommender_lastname']);
                $subscriber->setRecommendationMessage($recommendation['message']);
                $subscriber->subscribe($recommendation['email']);

                if (count($session->getMessages()->getErrors()) == 0) {
                    $session->addData(array('subscriber' => false)); //delete session vars
                    $session->addSuccess($this->__('Die dobramoda.pl Newsletter-Empfehlung wurde erfolgreich versendet!'));
                    return $this->_redirect('*/*', array('_fragment' => 'recommend'));
                }

            } catch (Exception $e) {
                //set data for prefill of the form
                $subscriber->setPrefix($recommendation['prefix']);
                $subscriber->setFirstname($recommendation['firstname']);
                $subscriber->setLastname($recommendation['lastname']);
                $subscriber->setDob('1970-01-01');
                $subscriber->setEmail($recommendation['email']);
                $subscriber->setRecommenderFirstname($recommendation['recommender_firstname']);
                $subscriber->setRecommenderLastname($recommendation['recommender_lastname']);
                $subscriber->setRecommendationMessage($recommendation['message']);
                $session->addData(array('subscriber' => $subscriber));
                $session->addException($e, $this->__($e->getMessage()));
            }
        }
        $this->_redirect('*/*', array('_fragment' => 'recommend'));
    }

    public function successNewAction()
    {
        Mage::getSingleton('core/session')->addSuccess($this->__('Successfully refered'));
        return $this->_redirect('*/*/');
    }

    public function successEditAction()
    {
        Mage::getSingleton('core/session')->addSuccess($this->__('Ihre Daten wurden übermittelt.'));
        return $this->_redirect('*/*/');
    }

    public function successCancelAction()
    {
        Mage::getSingleton('core/session')->addSuccess($this->__('You have been unsubscribed.'));
        return $this->_redirect('*/*/');
    }

    public function saveAction()
    {
        $id = $this->getRequest()->getPost('id');
        $data = $this->getRequest()->getParam('newsletter_subscribe');

        if ($id) {
            $subscriber = Mage::getModel('newsletter/subscriber')->load($id);

            // allowed to change?
            if ($subscriber->getCustomerId() == Mage::getSingleton('customer/session')->getCustomer()->getId()) {
                $subscriber
                    ->setPrefix($data['prefix'])
                    ->setFirstname($data['firstname'])
                    ->setLastname($data['lastname'])
                    ->setDob($data['day'] . '.' . $data['month'] . '.' . $data['year']);
                if (isset($data['animal'])) $subscriber->setAnimal($data['animal']);
                if (isset($data['animal_name'])) $subscriber->setAnimalName($data['animal_name']);

                $subscriber
                //->setChangeStatusAt(now())
                    ->setSubscriberStatus($data['subscriber_status'])
                    ->save();
            }

        } else {
            $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($data['email']);
            $subscriber
                ->setPrefix($data['prefix'])
                ->setFirstname($data['firstname'])
                ->setLastname($data['lastname'])
                ->setDob($data['day'] . '.' . $data['month'] . '.' . $data['year'])
                ->setAnimal($data['animal'])
                ->setAnimalName($data['animal_name']);

            //$status = $subscriber->subscribe($data['email']);
        }
        return $this->_redirect('newsletter/manage/');
    }

    /**
     * Subscription confirm action
     */
    public function confirmAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $code = (string)$this->getRequest()->getParam('code');

        if ($id && $code) {
            $subscriber = Mage::getModel('newsletter/subscriber')->load($id);
            $session = Mage::getSingleton('core/session');

            if ($subscriber->getId() && $subscriber->getCode()) {
                if ($subscriber->confirm($code)) {
                    //send email for confirmation, which seems not to be standard when double opt in is enabled
                    $subscriber->sendConfirmationSuccessEmail();
                    Mage::log('send success email');
                    $session->addSuccess($this->__('Your subscription has been confirmed.'));
                } else {
                    $session->addError($this->__('Invalid subscription confirmation code.'));
                }
            } else {
                $session->addError($this->__('Invalid subscription ID.'));
            }
        }

        $this->_redirectUrl(Mage::getBaseUrl());
    }
}