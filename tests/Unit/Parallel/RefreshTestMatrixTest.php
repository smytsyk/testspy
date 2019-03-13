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

use File_Iterator_Facade;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use TestSpy\Parallel\Config\Config;
use TestSpy\Parallel\Exception\TestMatrixRefreshIsUnderLockException;
use TestSpy\Parallel\RefreshRequest;
use TestSpy\Parallel\RefreshTestMatrix;
use TestSpy\SyncControl;
use TestSpy\Utilities;

class RefreshTestMatrixTest extends TestCase
{

    /**
     * @var SyncControl
     */
    private $syncControl;

    /**
     * @var Utilities
     */
    private $utilities;

    /**
     * @var File_Iterator_Facade
     */
    private $fileIteratorFacade;

    /**
     * @var RefreshRequest
     */
    private $request;

    /**
     * @var RefreshTestMatrix
     */
    private $sut;

    public function setUp()
    {
        $this->syncControl        = $this->prophesize(SyncControl::class);
        $this->utilities          = $this->prophesize(Utilities::class);
        $this->fileIteratorFacade = $this->prophesize(File_Iterator_Facade::class);
        $this->sut                = new RefreshTestMatrix(
            $this->syncControl->reveal(),
            $this->utilities->reveal(),
            $this->fileIteratorFacade->reveal()
        );

        $config = new Config(
            'config.yml',
            'testSpyParallel',
            '/codebase_dir',
            'FQCN',
            'phpunit',
            '/dummy_dir',
            'Test.php',
            'bootstrap.php',
            'unit.xml',
            '/tmp/dummy'
        );

        $this->request = new RefreshRequest($config, 1, 100);

        $this->fileIteratorFacade->getFilesAsArray('/dummy_dir', 'Test.php')->willReturn(['FileTest.php']);
        $this->utilities->unlink(Argument::cetera())->willReturn();
        $this->utilities->touch(Argument::any())->willReturn();
        $this->utilities->filePutContents(Argument::cetera())->willReturn();

    }

    public function testStartTestExecution()
    {
        $this->utilities->isFile(Argument::any())->willReturn(false);
        $this->utilities->exec('php -f testSpyParallel run-thread config.yml 1 100 0 1 > /dev/null 2>&1 &')->shouldBeCalled();

        $this->sut->execute($this->request);
    }

    public function testRefreshIsLocked()
    {
        $this->utilities->isFile(Argument::any())->willReturn(true);

        $this->expectException(TestMatrixRefreshIsUnderLockException::class);
        $this->sut->execute($this->request);
    }
}
