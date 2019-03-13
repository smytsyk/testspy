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

class SyncControl
{
    /**
     * @var TestSpyDaoInterface
     */
    private $dao;

    public function __construct(
        TestSpyDaoInterface $dao
    ) {
        $this->dao = $dao;
    }

    public function turnOn() : void
    {
        $this->dao->turnOn();
    }

    public function turnOff() : void
    {
        $this->dao->turnOff();

        $this->dao->clearAll();
    }

    public function isReady() : bool
    {
        return $this->dao->isReady();
    }

}
