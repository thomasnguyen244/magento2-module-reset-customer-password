<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Thomas\CustomerPassword\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Thomas\CustomerPassword\Helper\Data;

class Sending
{
    protected $resultPageFactory;
    protected $customer;
    protected $storeManager;
    protected $scopeConfig;
    protected $transportBuilder;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    protected $dataProcessor;
    protected $_customerRepository;
    protected $_mathRandom;
    protected $_accountmanagement;
    protected $helperData;
    protected $senderResolver;
    protected $logger;
    protected $collectionFactory;
    protected $customerResource;

    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customer,
        \Magento\Customer\Model\ResourceModel\Customer $customerResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Customer\Model\AccountManagement $accountmanagement,
        \Magento\Framework\Reflection\DataObjectProcessor $dataProcessor,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory,
        \Psr\Log\LoggerInterface $logger,
        Data $helperData
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;
        $this->customer = $customer;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->dataProcessor = $dataProcessor;
        $this->_accountmanagement = $accountmanagement;
        $this->_customerRepository = $customerRepository;
        $this->_mathRandom = $mathRandom;
        $this->customerRegistry = $customerRegistry;
        $this->senderResolver = $senderResolver ?? ObjectManager::getInstance()->get(SenderResolverInterface::class);
        $this->helperData = $helperData;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->customerResource = $customerResource;
    }

    /**
     * send customer email
     *
     * @param string|null $customerEmail
     * @param int $limit - default = 100
     * @return int
     */
    public function sendCustomerEmail($customerEmail = null, $limit = 100)
    {
        $count = 0;
        $data =  $customerEmail ? explode(",", $customerEmail) : [];
        $collection = $this->collectionFactory->create()
                            ->addAttributeToSelect('*');
        $collection->getSelect()
                ->where(
                    'is_active = ?',
                    1
                )
                ->where(
                    'is_sent = ?',
                    0
                );
        if ($data) {
            $collection->getSelect()
                ->where(
                    'email in (?)',
                    $data
                );
        }
        $collection->getSelect()->limit($limit);

        if ($collection->getSize()) {
            foreach ($collection as $_item) {
                $storeId = $_item->getStoreId();
                $emailTemplate = $this->helperData->getConfig("general/custom_reset_password", $storeId, "custom_reset_password");
                $customerModel = $this->_customerRepository->getById($_item->getId());
                try {
                    $this->sendEmailToCustomer($customerModel, $emailTemplate);
                    $this->runUpdateCustomer($_item->getId(), 1);
                    $count++;
                } catch (\Exception $e) {
                    //
                    //echo $e->getMessage();
                }
            }
        }
        return $count;
    }

    /**
     * reset customer is_sent
     *
     * @param string|null $customerEmail
     * @param int $limit - default = 100
     * @return int
     */
    public function resetCustomerIsSent($customerEmail = null, $limit = 100)
    {
        $count = 0;
        if ($limit > 0) {
            $data =  $customerEmail ? explode(",", $customerEmail) : [];
            $collection = $this->collectionFactory->create()
                                ->addAttributeToSelect('*');
            $collection->getSelect()
                    ->where(
                        'is_active = ?',
                        1
                    )
                    ->where(
                        'is_sent = ?',
                        0
                    );
            if ($data) {
                $collection->getSelect()
                    ->where(
                        'email in (?)',
                        $data
                    );
            }
            $collection->getSelect()->limit($limit);

            if ($collection->getSize()) {
                foreach ($collection as $_item) {
                    try {
                        $this->runUpdateCustomer($_item->getId(), 0);
                        $count++;
                    } catch (\Exception $e) {
                        //echo $e->getMessage();
                    }
                }
            }
        } else {
            try {
                $this->runResetAllCustomers();
            } catch (\Exception $e) {
                // echo $e->getMessage();
            }
        }
        return $count;
    }

    /**
     * run update customer is_sent
     *
     * @param int $isSent
     * @return void
     */
    public function runResetAllCustomers()
    {
        $collection = $this->collectionFactory->create();
        $connection = $collection->getConnection();
        $tableName = $collection->getTable("customer_entity");
        $data = ['is_sent' => 0];

        $connection->update($tableName, $data, ['is_sent = ?' => 1]);
    }

    /**
     * run update customer is_sent
     *
     * @param int $customerId
     * @param int $isSent
     * @return void
     */
    public function runUpdateCustomer($customerId, $isSent = 0)
    {
        $collection = $this->collectionFactory->create();
        $connection = $collection->getConnection();
        $tableName = $collection->getTable("customer_entity");
        $data = ['is_sent' => $isSent];

        $connection->update($tableName, $data, ['entity_id = ?' => $customerId]);
    }

    /**
     * send email to customer
     *
     * @param int $websiteId
     * @return int
     */
    public function getStoreId($websiteId)
    {
        return $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getId();
    }

    /**
     * send email to customer
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param int|string $emailTemplate
     * @return void
     */
    public function sendEmailToCustomer($customer, $emailTemplate)
    {
        $storeId = $customer->getStoreId();
        $storeId = $storeId ? $storeId : $this->getStoreId($customer->getWebsiteId());
        $customerEmailData = $this->getFullCustomerObject($customer);
        $templateParams = [];
        $templateParams['customer'] = $customerEmailData;
        $templateParams['store'] = $this->storeManager->getStore($storeId);

        $sender = \Magento\Customer\Model\EmailNotification::XML_PATH_FORGOT_EMAIL_IDENTITY;
        $sender = $this->helperData->getConfig("general\sender_identity", $storeId, $sender);
        $from = $this->senderResolver->resolve(
            $this->scopeConfig->getValue($sender, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId),
            $storeId
        );

        $customerName = $customer->getFirstname() . " " . $customer->getLastname();

        try {
            $transport = $this->transportBuilder->setTemplateIdentifier($emailTemplate)
                ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
                ->setTemplateVars($templateParams)
                ->setFrom($from)
                ->addTo($customer->getEmail(), $customerName)
                ->getTransport();
            $transport->sendMessage();
            $this->logger->info($customer->getEmail() . ' : Email Send Successfully.');
        } catch (\Exception $e) {
            $this->logger->error($customer->getEmail() . ' : Something went wrong when sending email');
        }
    }

    /**
     * get full customer object
     * @param \Magento\Customer\Model\Customer $customer
     * @return mixed|array
     */
    public function getFullCustomerObject($customer)
    {
        $mergedCustomerData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerData = $this->dataProcessor
            ->buildOutputDataArray($customer, CustomerInterface::class);
        $mergedCustomerData->addData($customerData);
        $customerName = $customer->getFirstname() . " " . $customer->getLastname();
        $mergedCustomerData->setData('name', $customerName);
        $tokenVal = $this->getToken($customer->getEmail(), $customer->getWebsiteId());
        $mergedCustomerData->setData('rp_token', $tokenVal);

        return $mergedCustomerData;
    }

    /**
     * get reset token
     *
     * @param string $email
     * @param int $websiteId
     * @return string|mixed
     */
    public function getToken($email, $websiteId)
    {
        $customer = $this->_customerRepository->get($email, $websiteId);
        $newPasswordToken = $this->_mathRandom->getUniqueHash();
        $this->_accountmanagement->changeResetPasswordLinkToken($customer, $newPasswordToken);
        return $newPasswordToken;
    }
}
