<?php

namespace Yokai\IzanamiBundle\EventListener;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Yokai\IzanamiBundle\Event\RegisterObservableEvent;
use Yokai\IzanamiBundle\IzanamiEvents;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class TriggerAnalyzeSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $objects = [];

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            IzanamiEvents::REGISTER_OBSERVABLE => 'onRegisterObservable',
            KernelEvents::TERMINATE => 'onKernelTerminate',
            ConsoleEvents::TERMINATE => 'onConsoleTerminate',
        ];
    }

    /**
     * Add an Observable entity to the list of objects to analyze.
     *
     * @param RegisterObservableEvent $event
     */
    public function onRegisterObservable(RegisterObservableEvent $event)
    {
        $object = $event->getObject();
        $objectHash = spl_object_hash($object);

        //Ensure that the object wont be registered twice
        if (isset($this->objects[$objectHash])) {
            return;
        }

        $this->objects[$objectHash] = $object;
    }

    /**
     * When Symfony HTTP process is about to end, the collected objects analyze is triggered.
     *
     * @param PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        $this->triggerAnalyze();
    }

    /**
     * When Symfony Console process is about to end, the collected objects analyze is triggered.
     *
     * @param ConsoleTerminateEvent $event
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        $this->triggerAnalyze();
    }

    /**
     * Trigger collected objects analyze.
     */
    private function triggerAnalyze()
    {
        $objects = array_values($this->objects);

        //There is no objects to analyze
        if (0 === count($objects)) {
            return;
        }

        $this->container->get('izanami.violation_manager')->analyze($objects);
    }
}
