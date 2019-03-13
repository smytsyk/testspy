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

use TestSpy\Utilities;

class FilesystemLocker
{
    private const LOCK_NAME = 'testSpy-sync-run-lock';

    /**
     * @var Utilities
     */
    private $utilities;

    /**
     * @var string
     */
    private $lockFilepath;

    public function __construct(string $lockDirPath, Utilities $utilities)
    {
        $this->utilities    = $utilities;
        $this->lockFilepath = $lockDirPath.self::LOCK_NAME;
    }

    public function acquire() : void
    {
        $this->utilities->touch($this->lockFilepath);
    }

    public function isAcquired() : bool
    {
        return $this->utilities->isFile($this->lockFilepath);
    }

    public function release() : void
    {
        $this->utilities->unlink($this->lockFilepath, true);
    }

    public function name() : string
    {
        return $this->lockFilepath;
    }
}
