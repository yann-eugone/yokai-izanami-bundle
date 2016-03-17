<?php

namespace Yokai\IzanamiBundle\DependencyInjection\Factory;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Yokai\IzanamiBundle\Violation\ViolationConfig;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class ViolationConfigDefinitionFactory
{
    /**
     * Create and return a ViolationConfig service definition.
     *
     * @param string $identifier
     * @param int    $severity
     * @param string $message
     * @param string $analyzer
     *
     * @return Definition
     */
    public function create($identifier, $severity, $message, $analyzer)
    {
        if (class_exists($analyzer)) {
            $analyzer = new Definition($analyzer);
        } else {
            $analyzer = new Reference($analyzer);
        }

        $definition = new Definition(
            ViolationConfig::class,
            [
                $identifier,
                $severity,
                $message,
                $analyzer,
            ]
        );

        $definition->addTag('izanami.violation_config');

        return $definition;
    }
}
