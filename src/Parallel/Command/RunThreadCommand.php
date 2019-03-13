<?php
/*
 * This file is part of smytsyk-dev/testspy.
 *
 * (c) Stas Mytsyk <denielm10@gmail.com>
 * (c) Vlad Mytsyk <vdmytsyk@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TestSpy\Parallel\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TestSpy\Parallel\Config\ConfigLoader;
use TestSpy\Parallel\ThreadRequest;
use TestSpy\Parallel\ThreadRunner;

class RunThreadCommand extends Command
{

    protected static $defaultName = 'run-thread';

    /**
     * @var ConfigLoader
     */
    private $configLoader;

    /**
     * @var ThreadRunner
     */
    private $threadRunner;

    public function __construct(ConfigLoader $configLoader, ThreadRunner $threadRunner)
    {
        $this->configLoader = $configLoader;
        $this->threadRunner = $threadRunner;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Run one thread to refresh of Test Matrix.')
            ->setHelp('It runs tests and saves information which files are loaded.')
            ->addArgument(
                'config',
                InputArgument::REQUIRED,
                'Path to parallel config.'
            )
            ->addArgument(
                'threads',
                InputArgument::REQUIRED,
                'The number of threads.'
            )
            ->addArgument(
                'batch-size',
                InputArgument::REQUIRED,
                'The batch size. How many tests run at once.'
            )
            ->addArgument(
                'offset',
                InputArgument::REQUIRED,
                'From which test to start.'
            )
            ->addArgument(
                'length',
                InputArgument::REQUIRED,
                'How many tests to run.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = new ThreadRequest(
            $this->configLoader->load($input->getArgument('config')),
            (int)$input->getArgument('offset'),
            (int)$input->getArgument('length'),
            (int)$input->getArgument('threads'),
            (int)$input->getArgument('batch-size')
        );

        $this->threadRunner->execute($request);
    }
}
