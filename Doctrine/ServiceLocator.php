<?php

namespace Yokai\IzanamiBundle\Doctrine;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class ServiceLocator
{
    /**
     * @var RegistryInterface[]
     */
    private $registries = [];

    /**
     * @param RegistryInterface $registry
     */
    public function addRegistry(RegistryInterface $registry)
    {
        $this->registries[] = $registry;
    }

    /**
     * @param string $class
     *
     * @return ObjectManager
     */
    public function getManager($class)
    {
        return $this->getManagerForClass($class);
    }

    /**
     * @param string $class
     *
     * @return ObjectRepository
     */
    public function getRepository($class)
    {
        return $this->getManagerForClass($class)->getRepository($class);
    }

    /**
     * @param string $class
     *
     * @return ClassMetadata
     */
    public function getMetadata($class)
    {
        return $this->getManagerForClass($class)->getClassMetadata($class);
    }

    /**
     * @param string $class
     *
     * @return ObjectManager
     */
    private function getManagerForClass($class)
    {
        foreach ($this->registries as $registry) {
            if ($manager = $registry->getManagerForClass($class)) {
                return $manager;
            }
        }

        throw new \RuntimeException('No manager for class '.$class);//todo
    }
}
