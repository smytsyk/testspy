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
use PHPUnit\Framework\TestSuite;
use TestSpy\SpyListener;
use TestSpy\TestSpyDaoInterface;
use TestSpy\Utilities;

class SpyListenerTest extends TestCase
{

    /**
     * @var TestSpyDaoInterface
     */
    private $dao;

    /**
     * @var Utilities
     */
    private $utilities;

    /**
     * @var TestSuite
     */
    private $testSuite;

    /**
     * @var SpyListener
     */
    private $sut;

    public function setUp()
    {
        $this->dao       = $this->prophesize(TestSpyDaoInterface::class);
        $this->utilities = $this->prophesize(Utilities::class);
        $this->utilities->getIncludedFiles()->willReturn(
            ['initially_included_file.php'],
            ['include/path/test_included_file.php', 'phpunit.phar:://test']
        );
        $this->sut = new SpyListener($this->dao->reveal(), 'include/path', $this->utilities->reveal());

        $this->testSuite = $this->prophesize(TestSuite::class);
    }

    public function testEndTestSuiteSetsFileTests()
    {
        $this->testSuite->getName()->willReturn(SpyListenerTest::class);
        $this->dao->getTestFilesBySut('test_included_file.php')->willReturn(['previously_saved_file.php' => true]);

        $fileTests = [
            'previously_saved_file.php'                                   => true,
            (new \ReflectionClass(SpyListenerTest::class))->getFileName() => true,
        ];

        $this->dao->setFile('test_included_file.php', $fileTests)->shouldBeCalled();
        $this->sut->endTestSuite($this->testSuite->reveal());
    }
}
