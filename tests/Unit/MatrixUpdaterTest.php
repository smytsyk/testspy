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
use TestSpy\MatrixUpdater;
use TestSpy\SyncControl;
use TestSpy\TestRunnerInterface;

class MatrixUpdaterTest extends TestCase
{
    public function testRunSuccessfully()
    {
        $syncControl = $this->prophesize(SyncControl::class);
        $testRunner  = $this->prophesize(TestRunnerInterface::class);
        $sut         = new MatrixUpdater($syncControl->reveal(), $testRunner->reveal());

        $sut->update();

        $syncControl->turnOn()->shouldHaveBeenCalled();
        $testRunner->run()->shouldHaveBeenCalled();
        $syncControl->turnOff()->shouldHaveBeenCalled();
    }
}
