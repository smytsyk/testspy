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

final class RefreshRequest
{
    /**
     * @var int
     */
    private $threads;
    /**
     * @var int
     */
    private $batchSize;

    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config, int $threads, int $batchSize)
    {
        $this->threads   = $threads;
        $this->batchSize = $batchSize;
        $this->config    = $config;
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

    /**
     * @return Config
     */
    public function config() : Config
    {
        return $this->config;
    }
}
