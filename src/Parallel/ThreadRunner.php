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

namespace TestSpy\Parallel;

use File_Iterator_Facade;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use TestSpy\PhpUnitConfigBuilder;
use TestSpy\SyncControl;
use TestSpy\Utilities;

final class ThreadRunner implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var SyncControl
     */
    private $syncControl;

    /**
     * @var Utilities
     */
    private $utilities;

    /**
     * @var File_Iterator_Facade
     */
    private $fileIteratorFacade;

    public function __construct(
        SyncControl $syncControl,
        Utilities $utilities,
        File_Iterator_Facade $fileIteratorFacade
    ) {
        $this->syncControl        = $syncControl;
        $this->utilities          = $utilities;
        $this->fileIteratorFacade = $fileIteratorFacade;

        $this->logger = new NullLogger();
    }

    public function execute(ThreadRequest $request) : void
    {
        $offset    = $request->getOffset();
        $length    = $request->getLength();
        $threads   = $request->getThreads();
        $batchSize = $request->getBatchSize();

        $files = $this->fileIteratorFacade->getFilesAsArray(
            $request->config()->testDirPath(),
            $request->config()->testSuffixes()
        );

        $files = array_slice($files, $offset, $length);

        $chunks = array_chunk($files, $batchSize);

        $configs = [];

        $countChunks = count($chunks);

        foreach ($chunks as $chunk => $files) {
            $builder = new PhpUnitConfigBuilder($request->config()->configXml());
            $builder->addListener(
                $request->config()->getFqcnListenerDao(),
                $request->config()->getCodebaseDirPath()
            );

            foreach ($files as $file) {
                $builder->addFile($file);
            }

            $conf = $builder->build($request->config()->testBootstrap());

            $configPath = $request->config()->tempPath().'spy_config_'.$chunk.'_'.$offset.'_'.$length.'.xml';
            $this->utilities->filePutContents($configPath, $conf);

            $configs[] = $configPath;
        }

        $currentConfig = 0;
        foreach ($configs as $xmlConfig) {
            $currentConfig++;

            $cmd = sprintf(
                '%s --configuration %s --testsuite testSpy',
                $request->config()->phpUnitRunnerCommand(),
                $xmlConfig
            );

            $this->logger->info($cmd);

            $this->utilities->exec($cmd);

            $this->logger->info('TestSpy execution is done ['.$currentConfig.'/'.$countChunks.']: '.$xmlConfig);
        }

        $this->markAsDone($request);

        $this->checkIfAllThreadsCompleted($request, $threads);
    }

    private function markAsDone(ThreadRequest $request) : void
    {
        $this->utilities->filePutContents($request->config()->threadProgressFile(), 'done'.PHP_EOL, FILE_APPEND);
    }

    public function checkIfAllThreadsCompleted(ThreadRequest $request, int $threads) : void
    {
        $results = $this->utilities->file($request->config()->threadProgressFile());

        if (count($results) === $threads) {
            $this->syncControl->turnOn();

            $this->logger->info('TestSpy sync completed');

            $this->utilities->filePutContents($request->config()->threadDoneFile(), '');

            $locker = new FilesystemLocker($request->config()->tempPath());
            $locker->release();
        }
    }
}
