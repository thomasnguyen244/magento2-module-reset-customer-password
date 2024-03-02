<?php
/**
 * Copyright © workwiththomas.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thomas\CustomerPassword\Observer\Backend\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Backend\Model\Session;
use Thomas\CustomerPassword\Helper\Data;

/**
 * Class AuthObserver
 *
 * @package Thomas\CustomerPassword\Observer\Backend\Controller
 */
class ActionPredispatchCustomerIndexSave implements \Magento\Framework\Event\ObserverInterface
{

    const CURRENT_USER_PASSWORD_FIELD = 'admin_password';

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $actionFlag;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * CustomerPassword data
     *
     * @var Data
     */
    protected $helper;

    /**
     * AuthObserver constructor.
     *
     * @param Context                     $context
     * @param Session                     $session
     * @param \Magento\Framework\Registry $registry
     * @param Data                        $helper
     */
    public function __construct(
        Context $context,
        Session $session,
        \Magento\Framework\Registry $registry,
        Data $helper
    ) {
        $this->messageManager = $context->getMessageManager();
        $this->url = $context->getUrl();
        $this->redirect = $context->getRedirect();
        $this->actionFlag = $context->getActionFlag();
        $this->session = $session;
        $this->registry = $registry;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $originalRequestData = $observer->getEvent()->getRequest()->getPostValue();
        $customer = new \Magento\Framework\DataObject($originalRequestData['customer']);
        $passwords = $customer->getPasswordSection();
        if (empty($passwords['password']) || !$this->helper->isEnablePasswordSection()) {
            return;
        }

        /* @var \Magento\Framework\App\Action\Action $controller */
        $controller = $observer->getControllerAction();

        $redirect = 0;
        /* @var $currentUser \Magento\Backend\Model\Auth\Session */
        $currentUser = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Backend\Model\Auth\Session')->getUser();

        /* Before updating customer data, ensure that password of current admin user is entered and is correct */
        $currentUserPasswordField = $this::CURRENT_USER_PASSWORD_FIELD;
        $isCurrentUserPasswordValid = isset($passwords[$currentUserPasswordField])
            && !empty($passwords[$currentUserPasswordField]) && is_string($passwords[$currentUserPasswordField]);
        try {
            if (!($isCurrentUserPasswordValid)) {
                throw new AuthenticationException(__('You have entered an invalid password for current admin user.'));
            }
            $currentUser->performIdentityCheck($passwords[$currentUserPasswordField]);
            $this->registry->register('current_admin_user', $currentUser);
        } catch (\Magento\Framework\Exception\AuthenticationException $e) {
            $this->messageManager->addError(__('You have entered an invalid password for current admin user.'));
            $redirect = 1;
        } catch (\Magento\Framework\Validator\Exception $e) {
            $messages = $e->getMessages();
            $this->messageManager->addMessages($messages);
            $redirect = 1;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($e->getMessage()) {
                $this->messageManager->addError($e->getMessage());
            }
            $redirect = 1;
        }

        // Redirect to customer edit form
        if ($redirect) {
            $customerId = $customer->getEntityId();
            if ($customerId) {
                $redirectUrl = $this->url->getUrl('customer/*/edit', ['id' => $customerId, '_current' => true]);
            } else {
                $redirectUrl = $this->url->getUrl('customer/*/new', ['_current' => true]);
            }

            $this->session->setCustomerFormData($originalRequestData);
            $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            $this->redirect->redirect($controller->getResponse(), $redirectUrl);
        }
    }
}

