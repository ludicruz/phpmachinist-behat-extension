<?php

namespace DerpTest\Behat\MachinistExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class MachinistExtension implements Extension
{
    public function getConfigKey()
    {
        return 'machinist';
    }

    public function process(ContainerBuilder $container)
    {
    }

    public function initialize(ExtensionManager $extensionManager)
    {
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('doctrine_orm')
                ->end()
                ->scalarNode('truncate_on_wipe')
                    ->defaultFalse()
                ->end()
                ->arrayNode('store')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->enumNode('type')
                                ->isRequired()
                                ->values(array(
                                    'sqlite',
                                    'mysql',
                                    'postgresql'
                                ))
                            ->end()
                            ->scalarNode('dsn')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('user')
                            ->end()
                            ->scalarNode('password')
                            ->end()
                            ->scalarNode('database')
                            ->end()
                            ->arrayNode('options')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('blueprint')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('store')
                            ->defaultValue('default')
                        ->end()
                        ->scalarNode('entity')
                        ->end()
                        ->variableNode('defaults')
                        ->end()
                        ->arrayNode('relationships')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                            ->children()
                                ->scalarNode('foreign')
                                    ->defaultValue('id')
                                ->end()
                                ->scalarNode('local')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $this->validateConfig($config);
        $this->processDefaults($config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__));
        $loader->load('core.xml');
        $container->setParameter('derptest.phpmachinist.behat.parameters', $config);
        $tagged = $container->findDefinition('derptest.phpmachinist.behat.context.initializer')
            ->addTag(ContextExtension::INITIALIZER_TAG);
    }

    protected function validateConfig(array $config)
    {
        // Blank for now
    }

    protected function processDefaults(array &$configs)
    {
        $hasDoctrine = false;
        if (array_key_exists('doctrine_orm', $configs)) {
            $this->processDoctrineDefaults($configs['doctrine_orm']);
            $hasDoctrine = true;
        }

        if (!empty($configs['store'])) {
            $this->processStoreDefaults($configs['store']);
        }

        if (!empty($configs['blueprint'])) {
            $this->processBlueprintDefaults($configs['blueprint']);
            // Process blueprints defaults separately from relationships to ensure
            // all blueprints exists before relating them
            $this->processRelationshipDefaults($configs['blueprint']);
        }
    }

    protected function processDoctrineDefaults(array &$doctrineConfigs) {
        if (!empty($doctrineConfigs)) {
            throw new Exception('not yet implemented');
        }
    }

    protected function processStoreDefaults(array &$storeConfigs)
    {
        foreach ($storeConfigs as $name => &$store) {
            if (empty($store['entity'])) {
                $store['entity'] = $name;
            }
        }

    }

    protected function processBlueprintDefaults(array &$blueprintConfigs)
    {
        foreach ($blueprintConfigs as $key => &$blueprint) {
            if (empty($blueprint['entity'])) {
                $blueprint['entity'] = $key;
            }

            if (empty($blueprint['store'])) {
                $blueprint['store'] = 'default';
            }
        }
    }

    protected function processRelationshipDefaults(array &$blueprintConfigs)
    {
        foreach ($blueprintConfigs as &$blueprint) {
            if (!empty($blueprint['relationships'])) {
                foreach ($blueprint['relationships'] as $name => &$relationship) {
                    if (empty($relationship['foreign'])) {
                        $relationship['foreign'] = 'id';
                    }
                    if (empty($relationship['local'])) {
                        $relationship['local'] = $blueprintConfigs[$name]['entity'] . 'Id';
                    }
                }
            }
        }
    }
}
