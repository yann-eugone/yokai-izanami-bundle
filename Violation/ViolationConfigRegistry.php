<?php

namespace Yokai\IzanamiBundle\Violation;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class ViolationConfigRegistry
{
    /**
     * @var ViolationConfig[]
     */
    private $config = [];

    /**
     * Add a configuration.
     *
     * @param ViolationConfig $config
     */
    public function add(ViolationConfig $config)
    {
        $this->config[] = $config;
    }

    /**
     * Get all configurations.
     *
     * @return ViolationConfig[]
     */
    public function all()
    {
        return $this->config;
    }

    /**
     * Get configurations that supports provided object.
     *
     * @param object $object
     *
     * @return ViolationConfig[]
     */
    public function getFor($object)
    {
        return array_filter(
            $this->config,
            function (ViolationConfig $config) use ($object) {
                return $config->getAnalyzer()->supports($object);
            }
        );
    }
}
