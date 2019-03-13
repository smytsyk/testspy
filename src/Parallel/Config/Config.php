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

namespace TestSpy\Parallel\Config;

class Config
{
    private $configParallel;
    private $parallelRunnerPath;
    private $codebaseDirPath;
    private $fqcnListenerDao;
    private $phpUnitRunnerCommand;
    private $tempPath;
    private $testDirPath;
    private $testSuffixes;
    private $testBootstrap;
    private $configXml;

    public function __construct(
        string $configParallel,
        string $parallelRunnerPath,
        string $codebaseDirPath,
        string $fqcnListenerDao,
        string $phpUnitRunnerCommand,
        string $testDirPath,
        string $testSuffixes,
        string $testBootstrap,
        string $configXml,
        string $tempPath
    ) {
        $this->configParallel       = $configParallel;
        $this->codebaseDirPath      = $codebaseDirPath;
        $this->fqcnListenerDao      = $fqcnListenerDao;
        $this->parallelRunnerPath   = $parallelRunnerPath;
        $this->phpUnitRunnerCommand = $phpUnitRunnerCommand;
        $this->testDirPath          = $testDirPath;
        $this->testSuffixes         = $testSuffixes;
        $this->testBootstrap        = $testBootstrap;
        $this->configXml            = $configXml;
        $this->tempPath             = $tempPath;
    }

    public function configParallel() : string
    {
        return $this->configParallel;
    }

    public function getCodebaseDirPath() : string
    {
        return $this->codebaseDirPath;
    }

    public function getFqcnListenerDao() : string
    {
        return $this->fqcnListenerDao;
    }

    public function runnerPath() : string
    {
        return $this->parallelRunnerPath;
    }

    public function threadLockFile() : string
    {
        return $this->tempPath.'thread_lock';
    }

    public function threadDoneFile() : string
    {
        return $this->tempPath.'thread_done';
    }

    public function threadProgressFile() : string
    {
        return $this->tempPath.'thread_progress';
    }

    public function testDirPath() : string
    {
        return $this->testDirPath;
    }

    public function testSuffixes() : string
    {
        return $this->testSuffixes;
    }

    public function testBootstrap() : string
    {
        return $this->testBootstrap;
    }

    public function configXml() : string
    {
        return $this->configXml;
    }

    public function tempPath() : string
    {
        return $this->tempPath;
    }

    public function phpUnitRunnerCommand() : string
    {
        return $this->phpUnitRunnerCommand;
    }
}