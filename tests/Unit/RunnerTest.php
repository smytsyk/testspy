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

namespace TestSpy\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use TestSpy\PhpUnitConfigBuilder;
use TestSpy\Runner;
use TestSpy\RunnerConfig;
use TestSpy\TestSpyDaoInterface;
use TestSpy\Utilities;

class RunnerTest extends TestCase
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
     * @var Runner
     */
    private $subject;

    /**
     * @var Utilities
     */
    private $utilities;

    protected function setUp()
    {
        $this->dao = $this->prophesize(TestSpyDaoInterface::class);
        $this->dao->isReady()->willReturn(true);
        $this->dao->getTests()->willReturn([]);

        $this->configBuilder = $this->prophesize(PhpUnitConfigBuilder::class);
        $this->configBuilder->addFile(Argument::any());
        $this->configBuilder->build(Argument::any())->willReturn('xml data');

        $this->utilities = $this->prophesize(Utilities::class);
        $this->utilities->filePutContents(Argument::cetera())->willReturn(true);

        $this->subject = new Runner(
            $this->dao->reveal(),
            $this->configBuilder->reveal(),
            $this->utilities->reveal(),
            new RunnerConfig(
                'autoload.php',
                './customPath/vendor/bin/phpunit'
            )
        );
    }

    /**
     * @test
     */
    public function itTakesTestFileMapFromCache()
    {
        $this->dao->getTestFilesBySut('file1.php')->willReturn([]);
        $this->dao->getTestFilesBySut('file2.php')->willReturn([]);

        $this->subject->buildConfig(...['file1.php', 'file2.php']);

        $this->dao->getTestFilesBySut(Argument::any())->shouldHaveBeenCalledTimes(2);
    }

    /**
     * @test
     */
    public function itAddsTestFilesFromMapToBuilder()
    {
        $this->dao->getTestFilesBySut('file1.php')->willReturn(
            [
                'unitTestFile.php' => '',
                'nextTestFile.php' => '',
            ]
        );

        $this->subject->buildConfig(...['file1.php']);

        $this->configBuilder->addFile('unitTestFile.php')->shouldHaveBeenCalled();
        $this->configBuilder->addFile('nextTestFile.php')->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function itBuildsConfigAndRunsTests()
    {
        $this->dao->getTestFilesBySut('file1.php')->willReturn(
            [
                'unitTestFile.php',
                'nextTestFile.php',
            ]
        );

        $this->dao->getTestFilesBySut('file2.php')->willReturn(
            [
                'unitTestFile.php',
                'nextTestFile.php',
            ]
        );

        $this->configBuilder->build('autoload.php')->willReturn('xml data');

        $this->subject->buildConfig(...['file1.php', 'file2.php',]);

        $this->configBuilder->build('autoload.php')->shouldHaveBeenCalled();
        $this->utilities->filePutContents(Argument::any(), 'xml data')->shouldHaveBeenCalled();
    }

    public function itBuildsConfig()
    {
        $this->dao->getTestFilesBySut('file1.php')->willReturn(
            [
                'unitTestFile.php',
                'nextTestFile.php',
            ]
        );

        $this->dao->getTestFilesBySut('file2.php')->willReturn(
            [
                'unitTestFile.php',
                'nextTestFile.php',
            ]
        );

        $this->configBuilder->build('autoload.php')->willReturn('xml data');

        $configPath = $this->subject->buildConfig(...['file1.php', 'file2.php',]);

        $this->assertNotEmpty($configPath);

        $this->configBuilder->build(Argument::any())->shouldHaveBeenCalled();
        $this->utilities->filePutContents(Argument::any(), 'xml data')->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function itReturnsOriginalConfigIfSyncIsInProgress()
    {
        $this->dao->isReady()->willReturn(false);

        $this->configBuilder->phpUnitConfigXmlPath()->willReturn('xmlPath.xml');

        $configPath = $this->subject->buildConfig(...['abc']);

        $this->assertSame('xmlPath.xml', $configPath);
    }

}
