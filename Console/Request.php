<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUClient\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Magento\Framework\App\ResourceConnection;

/**
 * Class Request
 * @package Magefan\DSUClient\Console
 */
class Request extends Command
{
    /**
     * @var \Magefan\DSUClient\Model\Request
     */
    protected $getRequest;
    /**
     * @var ResourceConnection
     */
    protected $connection;

    /**
     * Request constructor.
     * @param \Magefan\DSUClient\Model\Request $getRequest
     * @param ResourceConnection $connection
     */
    public function __construct(
        \Magefan\DSUClient\Model\Request $getRequest,
        ResourceConnection $connection
    ) {
        $this->getRequest = $getRequest;
        $this->connection = $connection;
        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('dsu:request');
        $this->setDescription('Get Request Access')
            ->addArgument(
                'your_name',
                InputArgument::REQUIRED,
                'Email'
            )
            ->addArgument(
                'your_email',
                InputArgument::REQUIRED,
                'Admin Secret'
            );
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getRequest->send(
            $input->getArgument('your_name'),
            $input->getArgument('your_email')
        );
        $message = __('Request has been sent successfully. Please wait until moderator approve it.');
        $output->writeln((string)$message);
    }
}
