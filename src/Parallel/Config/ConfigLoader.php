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

namespace TestSpy\Parallel\Config;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

class ConfigLoader
{
    public function load(string $config)
    {

        $configDir = dirname($config);
        $filename  = basename($config);

        $locator = new FileLocator([$configDir]);

        $loader       = new YamlConfigLoader($locator);
        $configValues = $loader->load($locator->locate($filename));

        $processor  = new Processor();
        $configTree = new ConfigTree();

        $configData = $processor->processConfiguration(
            $configTree,
            $configValues
        );

        return new Config(
            $config,
            $configData[ConfigTree::PARALLEL_RUNNER_PATH],
            $configData[ConfigTree::CODEBASE_DIR_PATH],
            $configData[ConfigTree::FQCN_LISTENER_DAO],
            $configData[ConfigTree::PHPUNIT_RUNNER_COMMAND],
            $configData[ConfigTree::TEST_DIR_PATH],
            $configData[ConfigTree::TEST_SUFFIX],
            $configData[ConfigTree::TEST_BOOTSTRAP],
            $configData[ConfigTree::CONFIG_XML],
            $configData[ConfigTree::TEMP_PATH]
        );
    }
}
