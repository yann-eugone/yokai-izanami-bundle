<?php

namespace Yokai\IzanamiBundle\Violation;

use Doctrine\Common\Collections\Collection;
use Yokai\IzanamiBundle\Entity\Observable;
use Yokai\IzanamiBundle\Entity\Violation;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class ViolationContainer implements ViolationContainerInterface
{
    /**
     * @var ViolationConfig
     */
    private $config;

    /**
     * @var Observable
     */
    private $object;

    /**
     * @var Violation[]
     */
    private $insertions = [];

    /**
     * @var Violation[]
     */
    private $removals = [];

    /**
     * @var Violation[]
     */
    private $violations;

    /**
     * @param ViolationConfig $config
     * @param Observable      $object
     * @param Violation[]     $violations
     */
    public function __construct(ViolationConfig $config, Observable $object, $violations)
    {
        $violations = $violations instanceof Collection ? $violations->toArray() : $violations;

        $this->config = $config;
        $this->object = $object;
        $this->violations = $violations;
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @inheritDoc
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @inheritDoc
     */
    public function getViolations($filter = null)
    {
        $violations = $this->violations;

        //First filter only violations that are related to current config
        $violations = array_filter(
            $violations,
            function (Violation $violation) {
                return $violation->getType() === $this->config->getIdentifier();
            }
        );

        //If a custom filter is provided, it is applied to the violation list
        if (count($violations) > 0 && is_callable($filter)) {
            $violations = array_filter($violations, $filter);
        }

        return $violations;
    }

    /**
     * @inheritDoc
     */
    public function create(array $payload = [])
    {
        return new Violation(
            $this->config->getIdentifier(),
            $this->config->getSeverity(),
            $this->object,
            $this->config->getMessage(),
            $payload
        );
    }

    /**
     * @inheritDoc
     */
    public function add(Violation $violation)
    {
        $this->insertions[] = $violation;
    }

    /**
     * @inheritDoc
     */
    public function remove(Violation $violation)
    {
        $this->removals[] = $violation;
    }

    /**
     * @inheritDoc
     */
    public function insertions()
    {
        return $this->insertions;
    }

    /**
     * @inheritDoc
     */
    public function removals()
    {
        return $this->removals;
    }
}
