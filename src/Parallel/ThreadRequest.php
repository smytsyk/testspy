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

namespace TestSpy\Parallel;

use TestSpy\Parallel\Config\Config;

final class ThreadRequest
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $length;

    /**
     * @var int
     */
    private $threads;
    /**
     * @var int
     */
    private $batchSize;

    public function __construct(Config $config, int $offset, int $length, int $threads, int $batchSize)
    {
        $this->config    = $config;
        $this->offset    = $offset;
        $this->length    = $length;
        $this->threads   = $threads;
        $this->batchSize = $batchSize;
    }

    /**
     * @return Config
     */
    public function config() : Config
    {
        return $this->config;
    }

    /**
     * @return int
     */
    public function getOffset() : int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getLength() : int
    {
        return $this->length;
    }

    /**
     * @return int
     */
    public function getThreads() : int
    {
        return $this->threads;
    }

    /**
     * @return int
     */
    public function getBatchSize() : int
    {
        return $this->batchSize;
    }

}