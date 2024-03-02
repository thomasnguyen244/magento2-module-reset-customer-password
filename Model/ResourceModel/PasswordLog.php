<?php
/**
 * Copyright © workwiththomas.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thomas\CustomerPassword\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class PasswordLog extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('thomas_customerpassword_passwordlog', 'passwordlog_id');
    }
}

