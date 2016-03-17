<?php

namespace Yokai\IzanamiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Yokai\IzanamiBundle\DependencyInjection\Factory\ViolationConfigDefinitionFactory;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class YEEIzanamiExtension extends Extension
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
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($this->name);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $this->registerDoctrineRegistries($container);
        $this->registerViolationConfiguration($container, $config);
    }

    /**
     * Register all existing Doctrine registries services to the "izanami.doctrine_service_locator" service.
     *
     * @param ContainerBuilder $container
     */
    private function registerDoctrineRegistries(ContainerBuilder $container)
    {
        $doctrineServiceLocator = $container->getDefinition('izanami.doctrine_service_locator');

        $bundles = $container->getParameter('kernel.bundles');

        if (in_array('Doctrine\Bundle\DoctrineBundle\DoctrineBundle', $bundles)) {
            $doctrineServiceLocator->addMethodCall('addRegistry', [new Reference('doctrine')]);
        }

        if (in_array('Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle', $bundles)) {
            $doctrineServiceLocator->addMethodCall('addRegistry', [new Reference('doctrine_mongodb')]);
        }

        if (in_array('Doctrine\Bundle\PHPCRBundle\DoctrinePHPCRBundle', $bundles)) {
            $doctrineServiceLocator->addMethodCall('addRegistry', [new Reference('doctrine_phpcr')]);
        }
    }

    /**
     * Register all violations configuration from bundle config.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function registerViolationConfiguration(ContainerBuilder $container, array $config)
    {
        $factory = new ViolationConfigDefinitionFactory();

        foreach ($config['violations'] as $name => $violation) {
            $definition = $factory->create(
                $violation['identifier'],
                $violation['severity'],
                $violation['message'],
                $violation['analyzer']
            );

            $container->setDefinition(
                sprintf('izanami.%s_violation_config', $name),
                $definition
            );
        }
    }
}
