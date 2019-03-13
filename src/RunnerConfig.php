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

class RunnerConfig
{
    /**
     * @var string
     */
    private $bootstrapPath;

    /**
     * @var string[]
     */
    private $testPathRegexPatterns;

    /**
     * @var string
     */
    private $phpUnitConfigDir;

    public function __construct(
        string $bootstrapPath,
        string $phpUnitConfigDir = '/tmp/',
        array $testPathRegexPatterns = []
    ) {
        $this->bootstrapPath         = $bootstrapPath;
        $this->phpUnitConfigDir      = $phpUnitConfigDir;
        $this->testPathRegexPatterns = $testPathRegexPatterns;
    }

    /**
     * @return string
     */
    public function bootstrapPath() : string
    {
        return $this->bootstrapPath;
    }

    /**
     * @return string
     */
    public function phpUnitConfigDir() : string
    {
        return $this->phpUnitConfigDir;
    }

    /**
     * @return string[]
     */
    public function testPathRegexPatterns() : array
    {
        return $this->testPathRegexPatterns;
    }

}
