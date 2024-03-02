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

//@codingStandardsIgnoreFile

namespace Thomas\CustomerPassword\Ui\Component\Form\Fieldset;

use Thomas\CustomerPassword\Helper\Data;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class PasswordSection extends \Magento\Ui\Component\Form\Fieldset
{
    /**
     * CustomerPassword data
     *
     * @var Data
     */
    protected $helper;

    /**
     * PasswordSection constructor.
     * @param ContextInterface $context
     * @param Data $helper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Data $helper,
        $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->helper = $helper;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepare()
    {
        parent::prepare();
        if (!$this->helper->isEnablePasswordSection()) {
            $this->_data['config']['componentDisabled'] = true;
        }
    }
}
