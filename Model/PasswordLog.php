<?php
/**
 * Copyright © workwiththomas.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thomas\CustomerPassword\Model;

use Magento\Framework\Model\AbstractModel;
use Thomas\CustomerPassword\Api\Data\PasswordLogInterface;

class PasswordLog extends AbstractModel implements PasswordLogInterface
{
    protected $_eventPrefix = 'thomas_customer_password_log';

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Thomas\CustomerPassword\Model\ResourceModel\PasswordLog::class);
    }

    /**
     * @inheritDoc
     */
    public function getPasswordlogId()
    {
        return $this->getData(self::PASSWORDLOG_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPasswordlogId($passwordlogId)
    {
        return $this->setData(self::PASSWORDLOG_ID, $passwordlogId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerEmail()
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * @inheritDoc
     */
    public function getAdminUsername()
    {
        return $this->getData(self::ADMIN_USERNAME);
    }

    /**
     * @inheritDoc
     */
    public function setAdminUsername($adminUsername)
    {
        return $this->setData(self::ADMIN_USERNAME, $adminUsername);
    }

    /**
     * @inheritDoc
     */
    public function getAdminId()
    {
        return $this->getData(self::ADMIN_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAdminId($adminId)
    {
        return $this->setData(self::ADMIN_ID, $adminId);
    }

    /**
     * @inheritDoc
     */
    public function getAdminName()
    {
        return $this->getData(self::ADMIN_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setAdminName($adminName)
    {
        return $this->setData(self::ADMIN_NAME, $adminName);
    }

    /**
     * @inheritDoc
     */
    public function getIp()
    {
        return $this->getData(self::IP);
    }

    /**
     * @inheritDoc
     */
    public function setIp($ip)
    {
        return $this->setData(self::IP, $ip);
    }

    /**
     * @inheritDoc
     */
    public function getLoggedAt()
    {
        return $this->getData(self::LOGGED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setLoggedAt($loggedAt)
    {
        return $this->setData(self::LOGGED_AT, $loggedAt);
    }
}

