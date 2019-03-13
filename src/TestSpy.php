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

class TestSpy
{
    /**
     * @var Runner
     */
    private $runner;

    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    public function buildConfig(string ...$modifiedFiles)
    {
        return $this->runner->buildConfig(...$modifiedFiles);
    }

}
