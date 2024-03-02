<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Thomas\CustomerPassword\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Thomas\CustomerPassword\Helper\Data as Helper;

class ResetPassword extends Command
{

    protected $state;

    /**
     * @var \Thomas\CustomerPassword\Model\Sending
     */
    protected $sendingEmail;

    /**
     * @param Helper $helper
     */
    public function __construct(
        \Thomas\CustomerPassword\Model\Sending $sendingEmail,
        \Magento\Framework\App\State $state
    ) {
        $this->sendingEmail = $sendingEmail;
        $this->state = $state;
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        $output->writeln("Start sending email reset password");
        $output->writeln("----------");

        $limit = (int)$input->getOption('limit');
        $limit = $limit ? $limit : 0;

        $emails = $input->getOption('emails');
        $emails = $emails ? $emails : "";

        $count = $this->sendingEmail->sendCustomerEmail($emails, $limit);

        $output->writeln("Running sent emails Success!");

        return $count;
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName("thomas:customer-password:reset-password");
        $this->setDescription("Send reset password emails to customers");

        $this->addOption(
            'limit',
            '--limit',
            InputOption::VALUE_OPTIONAL,
            'Command input limit number'
        );

        $this->addOption(
            'emails',
            '--emails',
            InputOption::VALUE_OPTIONAL,
            'Command input list customer email addresses'
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
