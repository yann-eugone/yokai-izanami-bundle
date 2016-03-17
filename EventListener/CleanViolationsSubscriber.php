<?php

namespace Yokai\IzanamiBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Yokai\IzanamiBundle\Entity\Observable;
use Yokai\IzanamiBundle\Entity\Violation;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class CleanViolationsSubscriber implements EventSubscriber
{
    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postRemove,
        ];
    }

    /**
     * Remove violations of Observable entities whenever such an entity is removed.
     *
     * @param LifecycleEventArgs $event
     */
    public function postRemove(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        if (!$entity instanceof Observable) {
            return;
        }

        $event->getEntityManager()
            ->getRepository(Violation::class)
            ->removeForObject($entity)
        ;
    }
}
