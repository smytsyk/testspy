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
use TestSpy\SyncControl;
use TestSpy\TestSpyDaoInterface;

class SyncControlTest extends TestCase
{
    /**
     * @var TestSpyDaoInterface
     */
    private $dao;

    /**
     * @var SyncControl
     */
    private $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->dao     = $this->prophesize(TestSpyDaoInterface::class);
        $this->subject = new SyncControl($this->dao->reveal());
    }

    /**
     * @test
     */
    public function itTurnsOffSyncAndCleansCache()
    {
        $this->subject->turnOff();
        $this->dao->turnOff()->shouldHaveBeenCalled();
        $this->dao->clearAll()->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function itTurnsOnSync()
    {
        $this->subject->turnOn();

        $this->dao->turnOn()->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function itIsReadyWhenSyncIsNotInProgress()
    {
        $this->dao->isReady()->willReturn(true);

        $isReady = $this->subject->isReady();

        $this->assertTrue($isReady);
    }

    /**
     * @test
     */
    public function itIsNotReadyWhenSyncIsInProgress()
    {
        $this->dao->isReady()->willReturn(false);

        $isReady = $this->subject->isReady();

        $this->assertFalse($isReady);
    }
}
