<?php

namespace Yokai\IzanamiBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Yokai\IzanamiBundle\DependencyInjection\CompilerPass\RegisterTaggedServiceCompilerPass;
use Yokai\IzanamiBundle\DependencyInjection\YEEIzanamiExtension;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class YokaiIzanamiBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(
                new RegisterTaggedServiceCompilerPass(
                    'izanami.violation_config_registry',
                    'izanami.violation_config',
                    'add'
                )
            )
        ;
    }

    /**
     * @inheritDoc
     */
    public function getContainerExtension()
    {
        return new YEEIzanamiExtension('yee_izanami');
    }
}
