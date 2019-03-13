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

namespace TestSpy;

use TestSpy\Exception\CannotSaveConfigException;

class Runner
{
    /**
     * @var TestSpyDaoInterface
     */
    private $dao;

    /**
     * @var PhpUnitConfigBuilder
     */
    private $configBuilder;

    /**
     * @var Utilities
     */
    private $utilities;

    /**
     * @var RunnerConfig
     */
    private $runnerConfig;

    public function __construct(
        TestSpyDaoInterface $dao,
        PhpUnitConfigBuilder $configBuilder,
        Utilities $utilities,
        RunnerConfig $runnerConfig
    )
    {
        $this->dao           = $dao;
        $this->configBuilder = $configBuilder;
        $this->utilities     = $utilities;
        $this->runnerConfig  = $runnerConfig;
    }

    /**
     * @param string[] ...$modifiedFiles
     * @return string
     * @throws CannotSaveConfigException
     */
    public function buildConfig(string ...$modifiedFiles): string
    {
        if(!$this->dao->isReady()){
            return $this->configBuilder->phpUnitConfigXmlPath();
        }

        $this->addFileToConfig(...array_keys($this->dao->getTests()));

        foreach ($modifiedFiles as $file) {
            if ($this->isTestFile($file)) {
                $this->addFileToConfig(...[$file]);

                continue;
            }

            $testFiles = array_keys($this->dao->getTestFilesBySut($file));

            if (is_array($testFiles)) {
                $this->addFileToConfig(...$testFiles);
            }
        }

        $configXml = $this->configBuilder->build($this->runnerConfig->bootstrapPath());

        $configPath = $this->phpUnitConfigPath($configXml);

        $isSaved = $this->utilities->filePutContents($configPath, $configXml);

        if (!$isSaved) {
            throw new CannotSaveConfigException('Filepath: ' . $configPath);
        }

        return $configPath;
    }

    private function isTestFile(string $testFile): bool
    {
        foreach ($this->runnerConfig->testPathRegexPatterns() as $pattern) {
            if (preg_match($pattern, $testFile)) {
                return true;
            }
        }

        return false;
    }

    private function addFileToConfig(string ...$testFiles)
    {
        foreach ($testFiles as $testFile) {
            $this->configBuilder->addFile($testFile);
        }
    }

    private function phpUnitConfigPath(string $configXml): string
    {
        return $this->runnerConfig->phpUnitConfigDir() . 'phpunit_' . md5($configXml) . '.xml';
    }
}
