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
use TestSpy\Parallel\Config\Config;
use TestSpy\Parallel\Config\ConfigLoader;
use TestSpy\Parallel\Exception\TestMatrixRefreshIsUnderLockException;
use TestSpy\Parallel\RefreshRequest;
use TestSpy\Parallel\RefreshTestMatrix;

class RefreshCommand extends Command
{

    private const DEFAULT_NUMBER_OF_THREADS = 2;
    private const DEFAULT_BATCH_SIZE        = 10;

    protected static $defaultName = 'refresh';

    /**
     * @var RefreshTestMatrix
     */
    private $refreshTestMatrix;

    /**
     * @var ConfigLoader
     */
    private $configLoader;

    public function __construct(ConfigLoader $configLoader, RefreshTestMatrix $threadRunner)
    {
        $this->configLoader      = $configLoader;
        $this->refreshTestMatrix = $threadRunner;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Starts refresh of Test Matrix.')
            ->setHelp('It will run test matrix refresh in background in multiple threads.');

        $this
            ->addArgument(
                'config',
                InputArgument::REQUIRED,
                'Path to parallel config.'
            )
            ->addArgument(
                'threads',
                InputArgument::OPTIONAL,
                'The number of threads.',
                self::DEFAULT_NUMBER_OF_THREADS
            )
            ->addArgument(
                'batch-size',
                InputArgument::OPTIONAL,
                'The batch size. How many tests run at once.',
                self::DEFAULT_BATCH_SIZE
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            ['====**** Begin refresh ****====']
        );

        $request = new RefreshRequest(
            $this->configLoader->load($input->getArgument('config')),
            (int)$input->getArgument('threads'),
            (int)$input->getArgument('batch-size')
        );

        try {
            $this->refreshTestMatrix->execute($request);
        } catch (TestMatrixRefreshIsUnderLockException $exception) {
            $output->writeln([$exception->getMessage()]);
        }
    }
}
