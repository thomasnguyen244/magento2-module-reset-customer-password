<?php
/**
 * Copyright © workwiththomas.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thomas\CustomerPassword\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PasswordLogRepositoryInterface
{

    /**
     * Save PasswordLog
     * @param \Thomas\CustomerPassword\Api\Data\PasswordLogInterface $passwordLog
     * @return \Thomas\CustomerPassword\Api\Data\PasswordLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Thomas\CustomerPassword\Api\Data\PasswordLogInterface $passwordLog
    );

    /**
     * Retrieve PasswordLog
     * @param string $passwordlogId
     * @return \Thomas\CustomerPassword\Api\Data\PasswordLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($passwordlogId);

    /**
     * Retrieve PasswordLog matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Thomas\CustomerPassword\Api\Data\PasswordLogSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete PasswordLog
     * @param \Thomas\CustomerPassword\Api\Data\PasswordLogInterface $passwordLog
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Thomas\CustomerPassword\Api\Data\PasswordLogInterface $passwordLog
    );

    /**
     * Delete PasswordLog by ID
     * @param string $passwordlogId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($passwordlogId);
}

