<?php

namespace Yokai\IzanamiBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class RegisterTaggedServiceCompilerPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $service;

    /**
     * @var string
     */
    private $tag;

    /**
     * @var string
     */
    private $method;

    /**
     * @param string $service
     * @param string $tag
     * @param string $method
     */
    public function __construct($service, $tag, $method)
    {
        $this->service = $service;
        $this->tag = $tag;
        $this->method = $method;
    }

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->service)) {
            return;
        }

        $references = new \SplPriorityQueue();
        foreach ($container->findTaggedServiceIds($this->tag) as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $references->insert(new Reference($id), $priority);
        }

        $definition = $container->getDefinition($this->service);

        foreach (iterator_to_array($references) as $reference) {
            $definition->addMethodCall(
                $this->method,
                [ $reference ]
            );
        }
    }
}
