<?php

namespace Yokai\IzanamiBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Yokai\IzanamiBundle\Entity\Observable;
use Yokai\IzanamiBundle\Entity\Violation;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class ObjectAnalyzedEvent extends Event
{
    /**
     * @var Observable
     */
    private $object;

    /**
     * @var Violation[]
     */
    private $violationsInserted;

    /**
     * @var Violation[]
     */
    private $violationRemoved;

    /**
     * @param Observable  $object
     * @param Violation[] $violationsInserted
     * @param Violation[] $violationRemoved
     */
    public function __construct(Observable $object, $violationsInserted = [], $violationRemoved = [])
    {
        $this->object = $object;
        $this->violationsInserted = $violationsInserted;
        $this->violationRemoved = $violationRemoved;
    }

    /**
     * @return Observable
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return Violation[]
     */
    public function getViolationsInserted()
    {
        return $this->violationsInserted;
    }

    /**
     * @return Violation[]
     */
    public function getViolationRemoved()
    {
        return $this->violationRemoved;
    }
}
