<?php
/**
 * Copyright © workwiththomas.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thomas\CustomerPassword\Api\Data;

interface PasswordLogSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get PasswordLog list.
     * @return \Thomas\CustomerPassword\Api\Data\PasswordLogInterface[]
     */
    public function getItems();

    /**
     * Set customer_id list.
     * @param \Thomas\CustomerPassword\Api\Data\PasswordLogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

