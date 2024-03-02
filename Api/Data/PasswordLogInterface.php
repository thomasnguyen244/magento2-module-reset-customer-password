<?php
/**
 * Copyright © workwiththomas.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thomas\CustomerPassword\Api\Data;

interface PasswordLogInterface
{

    const PASSWORDLOG_ID = 'passwordlog_id';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_EMAIL = 'customer_email';
    const ADMIN_ID = 'admin_id';
    const ADMIN_NAME = 'admin_name';
    const ADMIN_USERNAME = 'admin_username';
    const IP = 'ip';
    const LOGGED_AT = 'logged_at';

    /**
     * Get passwordlog_id
     * @return int|null
     */
    public function getPasswordlogId();

    /**
     * Set passwordlog_id
     * @param int $passwordlogId
     * @return \Thomas\CustomerPassword\PasswordLog\Api\Data\PasswordLogInterface
     */
    public function setPasswordlogId($passwordlogId);

    /**
     * Get customer_id
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param int $customerId
     * @return \Thomas\CustomerPassword\PasswordLog\Api\Data\PasswordLogInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get customer_email
     * @return string|null
     */
    public function getCustomerEmail();

    /**
     * Set customer_email
     * @param string $customerEmail
     * @return \Thomas\CustomerPassword\PasswordLog\Api\Data\PasswordLogInterface
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Get admin_username
     * @return string|null
     */
    public function getAdminUsername();

    /**
     * Set admin_username
     * @param string $adminUsername
     * @return \Thomas\CustomerPassword\PasswordLog\Api\Data\PasswordLogInterface
     */
    public function setAdminUsername($adminUsername);

    /**
     * Get admin_id
     * @return int|null
     */
    public function getAdminId();

    /**
     * Set admin_id
     * @param int $adminId
     * @return \Thomas\CustomerPassword\PasswordLog\Api\Data\PasswordLogInterface
     */
    public function setAdminId($adminId);

    /**
     * Get admin_name
     * @return string|null
     */
    public function getAdminName();

    /**
     * Set admin_name
     * @param string $adminName
     * @return \Thomas\CustomerPassword\PasswordLog\Api\Data\PasswordLogInterface
     */
    public function setAdminName($adminName);

    /**
     * Get ip
     * @return string|null
     */
    public function getIp();

    /**
     * Set ip
     * @param string $ip
     * @return \Thomas\CustomerPassword\PasswordLog\Api\Data\PasswordLogInterface
     */
    public function setIp($ip);

    /**
     * Get logged_at
     * @return string|null
     */
    public function getLoggedAt();

    /**
     * Set logged_at
     * @param string $loggedAt
     * @return \Thomas\CustomerPassword\PasswordLog\Api\Data\PasswordLogInterface
     */
    public function setLoggedAt($loggedAt);
}

