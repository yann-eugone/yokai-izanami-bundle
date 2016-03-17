<?php

namespace Yokai\IzanamiBundle\Violation;

use Yokai\IzanamiBundle\Entity\Observable;
use Yokai\IzanamiBundle\Entity\Violation;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
interface ViolationContainerInterface
{
    /**
     * @return ViolationConfig
     */
    public function getConfig();

    /**
     * @return Observable
     */
    public function getObject();

    /**
     * @param null|callable $filter
     *
     * @return Violation[]
     */
    public function getViolations($filter = null);

    /**
     * @param array $payload
     *
     * @return Violation
     */
    public function create(array $payload = []);

    /**
     * @param Violation $violation
     */
    public function add(Violation $violation);

    /**
     * @param Violation $violation
     */
    public function remove(Violation $violation);

    /**
     * @return Violation[]
     */
    public function insertions();

    /**
     * @return Violation[]
     */
    public function removals();
}
