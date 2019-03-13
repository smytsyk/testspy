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
use TestSpy\BranchDetector;
use TestSpy\Utilities;

class BranchDetectorTest extends TestCase
{
    public function testGetChangedFiles()
    {
        $utilities = $this->prophesize(Utilities::class);
        $utilities->exec(
            'cd /path/to/repo && git diff origin/master... --name-only'
        )->willReturn(
            [
                0 => 'README.md',
            ]
        );
        $sut          = new BranchDetector('/path/to/repo', $utilities->reveal());
        $actualResult = $sut->getChangedFiles();

        $this->assertEquals(
            [
                0 => 'README.md',
            ],
            $actualResult
        );
    }
}
