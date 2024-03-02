<?php
/**
 * Copyright © workwiththomas.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thomas\CustomerPassword\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

/**
 * Class PasswordManagement
 *
 * @package Thomas\CustomerPassword\Model
 */
class PasswordManagement
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var Encryptor
     */
    protected $encryptor;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Thomas\CustomerPassword\Model\PasswordLogFactory
     */
    public $passwordLogFactory;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * PasswordManagement constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerRegistry            $customerRegistry
     * @param Encryptor                   $encryptor
     * @param \Magento\Framework\Registry $registry
     * @param PasswordLogFactory          $passwordLogFactory
     * @param RemoteAddress               $remoteAddress
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerRegistry $customerRegistry,
        Encryptor $encryptor,
        \Magento\Framework\Registry $registry,
        \Thomas\CustomerPassword\Model\PasswordLogFactory $passwordLogFactory,
        RemoteAddress $remoteAddress
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerRegistry = $customerRegistry;
        $this->encryptor = $encryptor;
        $this->registry = $registry;
        $this->passwordLogFactory = $passwordLogFactory;
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * Change customer password by Email
     *
     * @param string $customerEmail
     * @param string $password
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function changePassword($customerEmail, $password)
    {
        $customer = $this->customerRepository->get($customerEmail);
        $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerSecure->setRpToken(null);
        $customerSecure->setRpTokenCreatedAt(null);
        $passwordHash = $this->encryptor->getHash($password, true);
        $customerSecure->setPasswordHash($passwordHash);
        $this->customerRepository->save($customer);
        $this->addPasswordChangeLog($customer);
    }

    /**
     * Change customer password by id
     *
     * @param int $customerId
     * @param int $password
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function changePasswordById($customerId, $password)
    {
        $customer = $this->customerRepository->getById($customerId);
        $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerSecure->setRpToken(null);
        $customerSecure->setRpTokenCreatedAt(null);
        $passwordHash = $this->encryptor->getHash($password, true);
        $customerSecure->setPasswordHash($passwordHash);
        $this->customerRepository->save($customer);
        $this->addPasswordChangeLog($customer);
    }

    /**
     * Save customer password change log
     *
     * @param $customer
     */
    public function addPasswordChangeLog($customer)
    {
        $logFactory = $this->passwordLogFactory->create();
        $logFactory->setCustomerId($customer->getId());
        $logFactory->setCustomerEmail($customer->getEmail());

        $adminUser = $this->getAdminUser();
        if ($adminUser) {
            $logFactory->setAdminUsername($adminUser->getUsername());
            $logFactory->setAdminId($adminUser->getId());
            $logFactory->setAdminName($adminUser->getFirstname() . ' ' . $adminUser->getLastname());
            $logFactory->setIp($this->remoteAddress->getRemoteAddress());
        }
        $logFactory->save();
    }

    /**
     * Retrieving current admin detail from registry
     *
     * @return string
     */
    public function getAdminUser()
    {
        return $this->registry->registry('current_admin_user');
    }
}
