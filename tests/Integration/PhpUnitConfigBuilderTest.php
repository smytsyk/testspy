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
use TestSpy\PhpUnitConfigBuilder;

class PhpUnitConfigBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function itCanAddCustomListener()
    {
        $configBuilder = new PhpUnitConfigBuilder(__DIR__.'/TestConfig.xml');
        $configBuilder->addListener('ttt', 'mmm');

        $configXml = $configBuilder->build('path');

        $this->assertContains('<listener class="\TestSpy\SpyListener"><arguments><object class="ttt"/><string>mmm</string></arguments></listener>', $configXml);
    }
}
