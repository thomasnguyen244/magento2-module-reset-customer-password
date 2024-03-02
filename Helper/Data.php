<?php
/**
 * WorkWithThomas
 *
 * Do not edit or add to this file if you wish to upgrade to newer versions in the future.
 * If you wish to customise this module for your needs.
 * Please contact us https://workwiththomas.com/contact/.
 *
 * @category   WorkWithThomas
 * @package    Thomas_CustomerPassword
 * @copyright  Copyright (C) 2024 WorkWithThomas,.Jsc (https://workwiththomas.com/)
 * @license    https://workwiththomas.com/magento2-extension-license/
 */

declare(strict_types=1);

namespace Thomas\CustomerPassword\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{

    const RESOURCE_ID = "Thomas_CustomerPassword::customerpassword";

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Authorization\PolicyInterface
     */
    public $policyInterface;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Authorization\PolicyInterface $policyInterface
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Authorization\PolicyInterface $policyInterface
    ) {
        $this->storeManager = $storeManager;
        $this->policyInterface = $policyInterface;
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

    /** Whether a module is enabled in the configuration or not
    *
    * @return boolean
    */
    public function isModuleEnabled()
    {
        if ($this->_moduleManager->isEnabled('Thomas_CustomerPassword')) {
            if ($this->isEnabled()) {
                return true;
            }
        }
        return false;
    }

   /**
    * Whether a module output is permitted by the configuration or not
    *
    * @return boolean
    */
    public function isOutputEnabled()
    {
        if ($this->_moduleManager->isOutputEnabled('Thomas_CustomerPassword')) {
            if ($this->isEnabled() && $this->isPasswordFieldEnabled()) {
                return true;
            }
        }
        return false;
    }

    /**
    * Whether a module is enabled by the configuration or not
    *
    * @return bool
    */
    public function isEnabled()
    {
        return (bool)$this->getConfig("general/enable");
    }

    /**
    * Whether a module is enabled by the configuration or not
    *
    * @return bool
    */
    public function isPasswordFieldEnabled()
    {
        return (bool)$this->getConfig("general/enable_field");
    }

    /**
    * Whether a CLI command is enabled by the configuration or not
    *
    * @return bool
    */
    public function isCliEnabled()
    {
        return (bool)$this->getConfig("general/enable_cli");
    }

    /**
    * @param mixed|null $user
    * @return bool
    */
    public function isAllowed($user = null)
    {
        if (!$user) {
            /* @var $currentUser \Magento\Backend\Model\Auth\Session */
            $user = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Backend\Model\Auth\Session')->getUser();
        }
        $role = $user->getRole();
        $permission = $this->policyInterface->isAllowed($role->getId(), self::RESOURCE_ID);
        if ($permission) {
            return true;
        }
        return false;
    }

    /**
    * Check password section is enable
    *
    * @return bool
    * @throws \Magento\Framework\Exception\LocalizedException
    */
    public function isEnablePasswordSection()
    {
        if ($this->isModuleEnabled() && $this->isOutputEnabled() && $this->isAllowed()) {
            return true;
        }
        return false;
    }

    /**
    * Check password section is enable
    *
    * @return bool
    * @throws \Magento\Framework\Exception\LocalizedException
    */
    public function isEnableCliCommand()
    {
        if ($this->isModuleEnabled() && $this->isCliEnabled()) {
            return true;
        }
        return false;
    }

}
