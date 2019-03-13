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
use TestSpy\Parallel\Exception\TestMatrixRefreshIsUnderLockException;
use TestSpy\SyncControl;
use TestSpy\Utilities;

final class RefreshTestMatrix implements LoggerAwareInterface
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
        $this->utilities          = $utilities;
        $this->fileIteratorFacade = $fileIteratorFacade;

        $this->logger      = new NullLogger();
        $this->syncControl = $syncControl;
    }

    public function execute(RefreshRequest $request) : void
    {
        $locker = new FilesystemLocker($request->config()->tempPath(), $this->utilities);

        $threads   = $request->getThreads();
        $batchSize = $request->getBatchSize();

        if ($locker->isAcquired()) {
            throw new TestMatrixRefreshIsUnderLockException('It is locked: '.$locker->name());
        }

        $this->syncControl->turnOff();

        $this->utilities->unlink($request->config()->threadDoneFile(), true);

        $locker->acquire();

        $files = $this->fileIteratorFacade->getFilesAsArray(
            $request->config()->testDirPath(),
            $request->config()->testSuffixes()
        );

        $count = count($files);

        $this->logger->info('Test files found: '.$count);

        $this->utilities->filePutContents($request->config()->threadProgressFile(), '');

        for ($i = 0; $i < $threads; $i++) {
            $offset = $i * $count / $threads;
            $length = $count / $threads;

            $cmd = sprintf(
                'php -f %s %s %s %d %d %d %d',
                $request->config()->runnerPath(),
                'run-thread',
                $request->config()->configParallel(),
                $threads,
                $batchSize,
                $offset,
                $length
            );

            $this->logger->info('Running: '.$cmd);

            $this->utilities->exec($cmd.' > /dev/null 2>&1 &');
        }
    }
}
