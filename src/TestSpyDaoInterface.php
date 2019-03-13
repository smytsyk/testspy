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

interface TestSpyDaoInterface
{
    public function setFile(string $fileName, array $fileTests) : void;

    public function getTestFilesBySut(string $fileName) : array;

    public function turnOn() : void;

    public function turnOff() : void;

    public function clearAll() : void;

    public function isReady() : bool;

    public function addTest(string $testPath) : void;

    public function getTests() : array;
}
