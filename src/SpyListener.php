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

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\DataProviderTestSuite;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;

class SpyListener implements TestListener
{

    /**
     * @var TestSpyDaoInterface
     */
    private $dao;

    /**
     * @var
     */
    private $includePath;

    /**
     * @var Utilities
     */
    private $utilities;

    /**
     * @var string[]
     */
    private $loadedFiles = [];

    public function __construct(?TestSpyDaoInterface $dao = null, string $includePath = null, Utilities $utilities = null)
    {
        $this->dao = $dao ?? new FileSystemTestSpyDao();

        $this->includePath = $includePath ?? '';

        $this->utilities = $utilities ?? new Utilities();

        $this->loadedFiles = $this->utilities->getIncludedFiles();
    }

    /**
     * A test started.
     *
     * @param Test $test
     */
    public function startTest(Test $test)
    {
    }

    /**
     * A test ended.
     *
     * @param Test  $test
     * @param float $time
     *
     * @throws \ReflectionException
     */
    public function endTest(Test $test, $time)
    {

    }

    private function vendorPath() : string
    {
        return (\getenv('COMPOSER_VENDOR_DIR') !== false) ? \getenv('COMPOSER_VENDOR_DIR') : '/vendor/';
    }

    /**
     * An error occurred.
     *
     * @param Test       $test
     * @param \Exception $e
     * @param float      $time
     */
    public function addError(Test $test, \Exception $e, $time)
    {
        $this->addTestToRun($test);
    }

    /**
     * A warning occurred.
     *
     * @param Test    $test
     * @param Warning $e
     * @param float   $time
     */
    public function addWarning(Test $test, Warning $e, $time)
    {
        $this->addTestToRun($test);
    }

    /**
     * A failure occurred.
     *
     * @param Test                 $test
     * @param AssertionFailedError $e
     * @param float                $time
     */
    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        $this->addTestToRun($test);
    }

    /**
     * Incomplete test.
     *
     * @param Test       $test
     * @param \Exception $e
     * @param float      $time
     */
    public function addIncompleteTest(Test $test, \Exception $e, $time)
    {
        $this->addTestToRun($test);
    }

    /**
     * Risky test.
     *
     * @param Test       $test
     * @param \Exception $e
     * @param float      $time
     */
    public function addRiskyTest(Test $test, \Exception $e, $time)
    {
        $this->addTestToRun($test);
    }

    private function addTestToRun(Test $test)
    {
        $testpath = \get_class($test);

        $reflector = new \ReflectionClass($testpath);
        $this->dao->addTest($reflector->getFileName());
    }

    /**
     * Skipped test.
     *
     * @param Test       $test
     * @param \Exception $e
     * @param float      $time
     */
    public function addSkippedTest(Test $test, \Exception $e, $time)
    {
    }

    /**
     * A test suite started.
     *
     * @param TestSuite $suite
     */
    public function startTestSuite(TestSuite $suite)
    {
    }

    /**
     * A test suite ended.
     *
     * @param TestSuite $suite
     */
    public function endTestSuite(TestSuite $suite)
    {
        if ($suite instanceof DataProviderTestSuite) {
            return;
        }

        try {
            $testFileName = (new \ReflectionClass($suite->getName()))->getFileName();
        } catch (\ReflectionException $e) {
            // It gets TestSpy class and tries to get class
            return;
        }

        $result = $this->utilities->getIncludedFiles();
        $result = array_diff($result, $this->loadedFiles);

        $sutFiles = [];

        foreach ($result as $filename) {
            if (strpos($filename, 'phpunit.phar') !== false) {
                continue;
            }

            if (strpos($filename, $this->vendorPath()) === false) {
                $sutFiles[$filename] = $filename;
            }
        }

        foreach ($sutFiles as $sutFile) {
            $sutFile   = str_replace($this->includePath.'/', '', $sutFile);
            $fileTests = $this->dao->getTestFilesBySut($sutFile);

            $fileTests[$testFileName] = true;

            $this->dao->setFile($sutFile, $fileTests);
        }

    }
}
