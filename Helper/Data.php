<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Thomas\CustomerPassword\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\File\Csv;
use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends AbstractHelper
{

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param string $key
     * @param null $store
     * @param null $default
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig($key, $store = null, $default = null)
    {
        $store = $this->storeManager->getStore($store);

        $result = $this->scopeConfig->getValue(
            'thomascustomerpassword/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        if ($default != null) {
            return $result ? $result : $default;
        } else {
            return $result;
        }
    }

}
