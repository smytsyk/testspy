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
use TestSpy\FileSystemTestSpyDao;

class FileSystemTestSpyDaoTest extends TestCase
{

    /**
     * @var FileSystemTestSpyDao
     */
    private $subject;

    protected function setUp()
    {
        $this->subject = new FileSystemTestSpyDao();
    }

    /**
     * @test
     */
    public function itCanSetAndGetValue()
    {
        $this->subject->setFile('abc', [1, 2, 3]);

        $result = $this->subject->getTestFilesBySut('abc');

        $this->assertSame([1, 2, 3], $result);
    }

    /**
     * @test
     */
    public function getTestFilesBySutReturnsEmptyArrayWhenValueIsNotSet()
    {
        $result = $this->subject->getTestFilesBySut('ddd');

        $this->assertSame([], $result);
    }

    /**
     * @test
     */
    public function itClearsInformationAboutAllTests()
    {
        $this->subject->setFile('EEE', [1, 2, 3]);
        $this->subject->clearAll();

        $result = $this->subject->getTestFilesBySut('EEE');

        $this->assertSame([], $result);
    }

    /**
     * @test
     */
    public function itTurnsOnSync()
    {
        $this->subject->turnOff();
        $this->subject->turnOn();

        $isReady = $this->subject->isReady();

        $this->assertTrue($isReady);
    }

    /**
     * @test
     */
    public function itTurnsOffSync()
    {
        $this->subject->turnOn();
        $this->subject->turnOff();

        $isReady = $this->subject->isReady();

        $this->assertFalse($isReady);
    }

}
