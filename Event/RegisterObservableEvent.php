<?php

namespace Yokai\IzanamiBundle\Event;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\Event;
use Yokai\IzanamiBundle\Entity\Observable;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class RegisterObservableEvent extends Event
{
    /**
     * @var Observable
     */
    private $object;

    /**
     * @var LifecycleEventArgs
     */
    private $event;

    /**
     * @param Observable         $object
     * @param LifecycleEventArgs $event
     */
    public function __construct(Observable $object, LifecycleEventArgs $event)
    {
        $this->object = $object;
        $this->event = $event;
    }

    /**
     * @return Observable
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return LifecycleEventArgs
     */
    public function getEvent()
    {
        return $this->event;
    }
}
