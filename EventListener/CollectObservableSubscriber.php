<?php

namespace Yokai\IzanamiBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Yokai\IzanamiBundle\Entity\Observable;
use Yokai\IzanamiBundle\Event\RegisterObservableEvent;
use Yokai\IzanamiBundle\IzanamiEvents;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class CollectObservableSubscriber implements EventSubscriber
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var bool
     */
    private $enabled = true;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    /**
     * Enable the entity collect process.
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * Disable the entity collect process.
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * Collect all Observable entities that are provided by the "postPersist" Doctrine lifecycle events.
     *
     * @param LifecycleEventArgs $event
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        if (!$this->enabled) {
            return;
        }

        $entity = $event->getEntity();
        if (!$entity instanceof Observable) {
            return;
        }

        $this->dispatcher->dispatch(
            IzanamiEvents::REGISTER_OBSERVABLE,
            new RegisterObservableEvent($entity, $event)
        );
    }

    /**
     * Collect all Observable entities that are provided by the "postUpdate" Doctrine lifecycle events.
     *
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(LifecycleEventArgs $event)
    {
        if (!$this->enabled) {
            return;
        }

        $entity = $event->getEntity();
        if (!$entity instanceof Observable) {
            return;
        }

        $this->dispatcher->dispatch(
            IzanamiEvents::REGISTER_OBSERVABLE,
            new RegisterObservableEvent($entity, $event)
        );
    }
}
