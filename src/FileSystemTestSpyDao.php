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

use Symfony\Component\Cache\Simple\FilesystemCache;

class FileSystemTestSpyDao implements TestSpyDaoInterface
{

    private const CACHE_NAMESPACE = 'TestSpy';
    private const TESTSPY_ON    = 'TestSpy-on';
    private const TESTSPY_TESTS = 'TestSpyTests';

    /**
     * @var FilesystemCache
     */
    protected $client;

    public function __construct(string $directory = null)
    {
        $this->client = new FilesystemCache(
            self::CACHE_NAMESPACE,
            0,
            $directory
        );
    }

    public function setFile(string $fileName, array $fileTests) : void
    {
        $fileKey = $this->generateFileKey($fileName);

        $this->client->set($fileKey, \json_encode($fileTests));
    }

    public function getTestFilesBySut(string $fileName) : array
    {
        $fileKey = $this->generateFileKey($fileName);

        $result = \json_decode($this->client->get($fileKey), true);

        if (!is_array($result)) {
            return [];
        }

        return $result;
    }

    protected function generateFileKey(string $fileName) : string
    {
        return self::CACHE_NAMESPACE.md5($fileName);
    }

    public function turnOn() : void
    {
        $this->client->set(self::TESTSPY_ON, 1);
    }

    public function turnOff() : void
    {
        $this->client->set(self::TESTSPY_ON, 0);
    }

    public function isReady() : bool
    {
        return (bool)$this->client->get(self::TESTSPY_ON, 0);
    }

    public function clearAll() : void
    {
        $this->client->clear();
    }

    public function addTest(string $testPath) : void
    {
        $tests = $this->getTests();

        $tests[$testPath] = $testPath;

        $this->client->set(self::TESTSPY_TESTS, \json_encode($tests));
    }

    public function getTests() : array
    {
        return (array)\json_decode($this->client->get(self::TESTSPY_TESTS), true);
    }
}
