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

namespace TestSpy\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use TestSpy\PhpUnitConfigBuilder;
use TestSpy\Runner;
use TestSpy\RunnerConfig;
use TestSpy\TestSpy;
use TestSpy\TestSpyDaoInterface;
use TestSpy\Utilities;

class TestSpyTest extends TestCase
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

    protected function setUp()
    {
        $this->dao = $this->prophesize(TestSpyDaoInterface::class);
        $this->dao->isReady()->willReturn(true);
        $this->dao->getTestFilesBySut(Argument::any())->willReturn([]);
        $this->dao->getTests()->willReturn([]);

        $this->configBuilder = new PhpUnitConfigBuilder(__DIR__.'/TestConfig.xml');
        $this->utilities     = $this->prophesize(Utilities::class);
        $this->utilities->filePutContents(Argument::cetera())->willReturn(true);
    }

    /**
     * @test
     *
     * @expectedException \TestSpy\Exception\WrongPhpUnitXmlConfigException
     */
    public function itThrowsExceptionWhenWrongPhpUnitConfigProvided()
    {
        $configBuilder = new PhpUnitConfigBuilder(__DIR__.'/WrongTestConfig.xml');

        $subject = new TestSpy(
            new Runner(
                $this->dao->reveal(),
                $configBuilder,
                $this->utilities->reveal(),
                new RunnerConfig('autoload.php')
            )
        );

        $subject->buildConfig(...['abc.php']);
    }

    /**
     * @test
     */
    public function itBuildsConfig()
    {
        $subject = new TestSpy(
            new Runner(
                $this->dao->reveal(),
                $this->configBuilder,
                $this->utilities->reveal(),
                new RunnerConfig('autoload.php')
            )
        );

        $this->dao->getTestFilesBySut('abc.php')->willReturn(
            [
                'tests/firstTest.php'  => '',
                'tests/secondTest.php' => '',
            ]
        );

        $subject->buildConfig('/root/path/autoload.php', ...['abc.php']);

        $this->utilities->filePutContents(
            Argument::any(),
            '<?xml version="1.0"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/7.4/phpunit.xsd" backupGlobals="true" backupStaticAttributes="false" bootstrap="autoload.php">
    <listeners>
        <listener class="MyListener" file="MyListener.php">
        </listener>
    </listeners>
    
<testsuites><testsuite name="testSpy"><file>tests/firstTest.php</file><file>tests/secondTest.php</file></testsuite></testsuites></phpunit>
'
        )->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function itAddsTestFilesToXmlFollowingPatternsOfPath()
    {
        $subject = new TestSpy(
            new Runner(
                $this->dao->reveal(),
                $this->configBuilder,
                $this->utilities->reveal(),
                new RunnerConfig(
                    'autoload.php',
                    '/tmp/',
                    ['/tests/', '/customTests/', '/[0-9]+/']
                )
            )
        );

        $subject->buildConfig(
            '/root/path/autoload.php',
            ...[
                'path/tests/mytest.php',
                '/customTests/mytest.php',
                '/13/mytest.php',
                'class-not-covered.php',
            ]
        );

        $this->utilities->filePutContents(
            Argument::any(),
            '<?xml version="1.0"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/7.4/phpunit.xsd" backupGlobals="true" backupStaticAttributes="false" bootstrap="autoload.php">
    <listeners>
        <listener class="MyListener" file="MyListener.php">
        </listener>
    </listeners>
    
<testsuites><testsuite name="testSpy"><file>path/tests/mytest.php</file><file>/customTests/mytest.php</file><file>/13/mytest.php</file></testsuite></testsuites></phpunit>
'
        )->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function itSavesConfigToSpecifiedDirectory()
    {
        $subject = new TestSpy(
            new Runner(
                $this->dao->reveal(),
                $this->configBuilder,
                $this->utilities->reveal(),
                new RunnerConfig(
                    'autoload.php',
                    '/tmp/',
                    []
                )
            )
        );

        $subject->buildConfig(
            '/root/path/autoload.php',
            ...['path/tests/mytest.php']
        );

        $phpUnitConfigBody = '<?xml version="1.0"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/7.4/phpunit.xsd" backupGlobals="true" backupStaticAttributes="false" bootstrap="autoload.php">
    <listeners>
        <listener class="MyListener" file="MyListener.php">
        </listener>
    </listeners>
    
<testsuites><testsuite name="testSpy"/></testsuites></phpunit>
';

        $this->utilities->filePutContents(
            '/tmp/phpunit_'.md5($phpUnitConfigBody).'.xml',
            $phpUnitConfigBody
        )->shouldHaveBeenCalled();
    }

    /**
     * @test
     *
     * @expectedException \TestSpy\Exception\CannotSaveConfigException
     */
    public function itThrowsExceptionWhenFileCannotBeSaved()
    {
        $subject = new TestSpy(
            new Runner(
                $this->dao->reveal(),
                $this->configBuilder,
                $this->utilities->reveal(),
                new RunnerConfig(
                    'autoload.php',
                    'tmp',
                    []
                )
            )
        );

        $this->utilities->filePutContents(
            Argument::any(),
            Argument::any()
        )->willReturn(false);

        $subject->buildConfig(
            '/root/path/autoload.php',
            ...['path/tests/mytest.php']
        );
    }
}
