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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigTree implements ConfigurationInterface
{
    public const PARALLEL_RUNNER_PATH   = 'parallel_runner_path';
    public const CODEBASE_DIR_PATH      = 'codebase_dir_path';
    public const FQCN_LISTENER_DAO      = 'fqcn_listener_dao';
    public const PHPUNIT_RUNNER_COMMAND = 'phpunit_runner_command';
    public const TEST_DIR_PATH          = 'test_dir_path';
    public const TEST_SUFFIX            = 'test_suffix';
    public const TEST_BOOTSTRAP         = 'test_bootstrap';
    public const CONFIG_XML             = 'config_xml';
    public const TEMP_PATH              = 'temp_path';

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('parallel');

        $rootNode
            ->children()
            ->scalarNode(self::PARALLEL_RUNNER_PATH)->isRequired()->end()
            ->scalarNode(self::CODEBASE_DIR_PATH)->isRequired()->end()
            ->scalarNode(self::FQCN_LISTENER_DAO)->isRequired()->end()
            ->scalarNode(self::PHPUNIT_RUNNER_COMMAND)->isRequired()->end()
            ->scalarNode(self::TEST_DIR_PATH)->isRequired()->end()
            ->scalarNode(self::TEST_SUFFIX)->isRequired()->end()
            ->scalarNode(self::TEST_BOOTSTRAP)->isRequired()->end()
            ->scalarNode(self::CONFIG_XML)->isRequired()->end()
            ->scalarNode(self::TEMP_PATH)->isRequired()->end();

        return $treeBuilder;
    }
}
