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

use RuntimeException;
use SimpleXMLElement;
use TestSpy\Exception\WrongPhpUnitXmlConfigException;

class PhpUnitConfigBuilder
{

    /**
     * @var string
     */
    private $phpUnitConfigXmlPath;

    private $config;

    /**
     * @var string[]
     */
    private $files = [];

    /**
     * @var string
     */
    private $pathToRepo;

    public function __construct(string $phpUnitConfigXmlPath, string $pathToRepo = null)
    {
        if (!extension_loaded('simplexml')) {
            throw new RuntimeException('It requires simplexml: http://php.net/manual/en/book.simplexml.php');
        }

        $this->phpUnitConfigXmlPath = $phpUnitConfigXmlPath;

        $config = new SimpleXMLElement($this->phpUnitConfigXmlPath, 0, true);

        $this->config = $config;

        $this->validateXmlConfig();

        $this->pathToRepo = $pathToRepo;
    }

    /**
     * @return string
     */
    public function phpUnitConfigXmlPath() : string
    {
        return $this->phpUnitConfigXmlPath;
    }

    public function addFile(string $file) : void
    {
        $this->files[$file] = $this->pathToRepo.$file;
    }

    public function addListener(string $fqcnOfListener, string $codebasePath) : void
    {
        $config = $this->config;

        if (!isset($config->listeners)) {
            $config->addChild('listeners');
        }

        $config->listeners->addChild('listener');

        $key                                          = count($config->listeners->children()) - 1;
        $config->listeners->children()[$key]['class'] = '\TestSpy\SpyListener';
        $config->listeners->children()[$key]->addChild('arguments');
        $config->listeners->children()[$key]->arguments->addChild('object');
        $config->listeners->children()[$key]->arguments[0]->object['class'] = $fqcnOfListener;
        $config->listeners->children()[$key]->arguments->addChild('string', $codebasePath);
    }

    public function build(string $bootstrapPath) : string
    {
        $config                          = $this->config;
        $config->attributes()->bootstrap = $bootstrapPath;

        // Clean all suites
        if (isset($config->testsuites)) {
            unset($config->testsuites);
        }

        $config->addChild('testsuites');

        $config->testsuites->addChild('testsuite', '');
        $suite = $config->testsuites->testsuite;

        $suite->addAttribute('name', 'testSpy');

        foreach ($this->files as $test) {
            $suite->addChild('file', $test);
        }

        return $config->asXML();
    }

    private function validateXmlConfig()
    {
        if ($this->config->getName() !== 'phpunit') {
            throw new WrongPhpUnitXmlConfigException('Phpunit XML config must start from tag phpunit');
        }
    }
}
