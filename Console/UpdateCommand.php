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
use Magefan\DSUClient\Model\Update;

/**
 * Class UpdateCommand
 * @package Magefan\DSUClient\Console
 */
class UpdateCommand extends Command
{
    /**
     * @var Update
     */
    protected $update;

    /**
     * @var string
     */
    protected $type;

    /**
     * UpdateCommand constructor.
     * @param Update $update
     * @param null $type
     * @param null $name
     * @param null $description
     */
    public function __construct(
        Update $update,
        $type = null,
        $name = null,
        $description = null
    ) {

        parent::__construct($name);
        $this->update = $update;
        $this->type = $type;
        $this->setDescription($description);
    }
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'Email'
            )
            ->addArgument(
                'secret',
                InputArgument::REQUIRED,
                'Secret'
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
        $attributeEmail = trim($input->getArgument('email'));
        $attributeSecret = trim($input->getArgument('secret'));
        $this->update->execute($attributeEmail, $attributeSecret, $this->type);
        $output->writeln(__('Finished Successfully.'));
        $output->writeln(__('Please run bin/magento app:config:import'));
        $output->writeln(__('Please run bin/magento indexer:reindex'));
    }
}
