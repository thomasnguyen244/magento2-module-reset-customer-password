<?php
/**
 * Copyright © workwiththomas.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thomas\CustomerPassword\Model\ResourceModel\PasswordLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'passwordlog_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Thomas\CustomerPassword\Model\PasswordLog::class,
            \Thomas\CustomerPassword\Model\ResourceModel\PasswordLog::class
        );
    }
}

