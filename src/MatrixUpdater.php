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

class MatrixUpdater
{

    /**
     * @var SyncControl
     */
    private $syncControl;
    /**
     * @var TestRunnerInterface
     */
    private $testRunner;

    public function __construct(SyncControl $syncControl, TestRunnerInterface $testRunner)
    {
        $this->syncControl = $syncControl;
        $this->testRunner  = $testRunner;
    }

    public function update()
    {
        $this->syncControl->turnOff();
        $this->testRunner->run();
        $this->syncControl->turnOn();
    }
}
