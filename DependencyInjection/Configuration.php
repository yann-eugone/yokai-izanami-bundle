<?php

namespace Yokai\IzanamiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->name);

        $rootNode
            ->children()
                ->append($this->getViolationsNode())
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * @return NodeDefinition
     */
    private function getViolationsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('violations');

        $node
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('identifier')->isRequired()->end()
                    ->scalarNode('severity')->isRequired()->end()
                    ->scalarNode('message')->isRequired()->end()
                    ->scalarNode('analyzer')->isRequired()->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
