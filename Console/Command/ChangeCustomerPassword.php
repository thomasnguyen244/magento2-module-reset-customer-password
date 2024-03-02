<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Thomas\CustomerPassword\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Thomas\CustomerPassword\Helper\Data;
use Thomas\CustomerPassword\Model\PasswordManagement;

/**
 * Class ChangeCustomerPassword
 *
 * @package Thomas\CustomerPassword\Console\Command
 */
class ChangeCustomerPassword extends Command
{
    /* Options name */
    const ARG_CUSTOMER_ID = 'customer-id';
    const ARG_CUSTOMER_EMAIL = 'customer-email';
    const ARG_CUSTOMER_PASSWORD = 'password';

    /**
     * @var PasswordManagement
     */
    protected $accountManagement;

    /**
     * @var State | \Magento\Framework\App\State
     */
    protected $state;

    /**
     * CustomerPassword data
     *
     * @var Data
     */
    protected $helper;

    /**
     * ChangeCustomerPassword constructor.
     *
     * @param PasswordManagement $accountManagement
     * @param State $state
     * @param Data $helper
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        PasswordManagement $accountManagement,
        State $state,
        Data $helper
    ) {
        $this->accountManagement = $accountManagement;
        $this->state = $state;
        $this->helper = $helper;
        parent::__construct();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        if (!$this->helper->isEnableCliCommand()) {
            $output->writeln("<error>The cli tool was disabled.");
            return 0;
        }
        //$this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        $customerId = $input->getOption(self::ARG_CUSTOMER_ID);
        $customerEmail = $input->getOption(self::ARG_CUSTOMER_EMAIL);
        $password = $input->getOption(self::ARG_CUSTOMER_PASSWORD);

        if ($password === null) {
            $output->writeln("<error>password --password <new password> is required field.");
        } elseif ($customerId === null && $customerEmail === null) {
            $output->writeln("<error>Enter either one of the field --customer-id <customer ID> or --customer-email <customer email></error>");
        } elseif ($customerId && $customerEmail) {
            $output->writeln("<error>Enter either one of the field --customer-id <customer ID> or --customer-email <customer email></error>");
        } else {
            try {
                $func = null;
                if ($customerEmail) {
                    $func = [$this->accountManagement, 'changePassword'];
                    $args = [$customerEmail, $password];
                } elseif ($customerId) {
                    $func = [$this->accountManagement, 'changePasswordById'];
                    $args = [$customerId, $password];
                }
                $this->state->emulateAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML, $func, $args);
                $output->writeln('Customer password has been changed.');
            } catch (\Exception $e) {
                $output->write($e->getMessage());
            }
        }
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("customer:password:change");
        $this->setDescription("Change customer password.");
        $this->addOption(
            self::ARG_CUSTOMER_PASSWORD,
            'p',
            InputOption::VALUE_REQUIRED,
            '(Required) Customer password'
        );
        $this->addOption(
            self::ARG_CUSTOMER_ID,
            'i',
            InputOption::VALUE_OPTIONAL,
            'Customer ID'
        );
        $this->addOption(
            self::ARG_CUSTOMER_EMAIL,
            'e',
            InputOption::VALUE_OPTIONAL,
            'Customer Email'
        );
        parent::configure();
    }

    /**
     * get short write
     *
     * @param string $in
     * @return string
     */
    protected function shortWrite($in)
    {
        $out = strlen($in) > 60 ? substr($in, 0, 60) . "..." : $in;
        return $out;
    }
}
