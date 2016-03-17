<?php

namespace Yokai\IzanamiBundle\Violation;

use Countable;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Yokai\IzanamiBundle\Entity\Observable;
use Yokai\IzanamiBundle\Entity\ViolationRepository;
use Yokai\IzanamiBundle\Event\ObjectAnalyzedEvent;
use Yokai\IzanamiBundle\IzanamiEvents;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class ViolationManager implements ViolationManagerInterface
{
    /**
     * @var ViolationConfigRegistry
     */
    private $config;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var ViolationRepository
     */
    private $repository;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ViolationConfigRegistry  $config
     * @param EntityManager            $manager
     * @param ViolationRepository      $repository
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface          $logger
     */
    public function __construct(
        ViolationConfigRegistry $config,
        EntityManager $manager,
        ViolationRepository $repository,
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger = null
    ) {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @inheritDoc
     */
    public function analyze($objects)
    {
        if ((is_array($objects) || $objects instanceof Countable) && count($objects) === 0) {
            $this->logger->debug('Calling analyzer with no entities to analyze. Abording...');

            return;
        }

        $insertions = [];
        $removals = [];

        //Iterate over objects
        foreach ($objects as $object) {
            //Ensure that the object implements the Observable interface
            if (!$object instanceof Observable) {
                continue;
            }

            //Fetch existing object violations
            $violations = $this->repository->getForObject($object);

            $objectInsertions = [];
            $objectRemovals = [];

            //Iterate over violation configurations
            foreach ($this->config->getFor($object) as $config) {
                $container = new ViolationContainer($config, $object, $violations);

                $this->logger->debug(
                    'Triggering violation analyze.',
                    [
                        $this->getConfigLogContext($config),
                        $this->getObjectLogContext($object),
                    ]
                );

                //Trigger the violation analyze
                try {
                    $config->getAnalyzer()->analyze($container);
                } catch (Exception $exception) {
                    $this->logger->error(
                        'An error occurred during violations analyze.',
                        array_merge(
                            $this->getConfigLogContext($config),
                            $this->getObjectLogContext($object),
                            $this->getExceptionLogContext($exception)
                        )
                    );

                    continue;
                }

                $this->logger->notice(
                    'Violation analyze terminated.',
                    [
                        'insertions' => count($container->insertions()),
                        'removals' => count($container->removals()),
                    ]
                );

                //Add collected violations to the object violations list
                $objectInsertions = array_merge($objectInsertions, $container->insertions());
                $objectRemovals = array_merge($objectRemovals, $container->removals());
            }

            //Add object violations to global violations list
            $insertions = array_merge($insertions, $objectInsertions);
            $removals = array_merge($removals, $objectRemovals);

            //Fire an event that tell that the analyze has ended
            $this->dispatcher->dispatch(
                IzanamiEvents::OBJECT_ANALYZED,
                new ObjectAnalyzedEvent($object, $objectInsertions, $objectRemovals)
            );
        }

        //Violations are collected, we need to call for persistence

        try {
            $violations = [];

            //Attempting to create/update collected violations
            $countInsertions = count($insertions);
            if ($countInsertions > 0) {
                $this->logger->notice(sprintf('Attempting to persist %d violations.', $countInsertions));

                foreach ($insertions as $violation) {
                    $this->manager->persist($violation);
                    $violations[] = $violation;
                }
            } else {
                $this->logger->debug('No violation to persist.');
            }

            //Attempting to delete collected violations
            $countRemovals = count($removals);
            if ($countRemovals > 0) {
                $this->logger->notice(sprintf('Attempting to remove %d violations.', $countRemovals));

                foreach ($removals as $violation) {
                    $this->manager->remove($violation);
                    $violations[] = $violation;
                }
            } else {
                $this->logger->debug('No violation to remove.');
            }

            if (count($violations) > 0) {
                $this->manager->flush($violations);
            }
        } catch (Exception $exception) {
            $this->logger->emergency(
                'An error occurred during violations saving operations.',
                $this->getExceptionLogContext($exception)
            );
        }
    }

    /**
     * Build the logging context of the ViolationConfig.
     *
     * @param ViolationConfig $config
     *
     * @return array
     */
    private function getConfigLogContext(ViolationConfig $config)
    {
        return [
            'analyzer' => get_class($config->getAnalyzer()),
            'violation' => $config->getIdentifier(),
        ];
    }

    /**
     * Build the logging context of an Exception.
     *
     * @param Exception $exception
     *
     * @return array
     */
    private function getExceptionLogContext(Exception $exception)
    {
        $context = [
            'code' => $exception->getCode(),
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'previous' => null,
        ];

        if ($previous = $exception->getPrevious()) {
            $context['previous'] = $this->getExceptionLogContext($previous);
        }

        return $context;
    }

    /**
     * Build the logging context of the analyze subject.
     *
     * @param Observable $object
     *
     * @return array
     */
    private function getObjectLogContext(Observable $object)
    {
        return [
            'object' => [
                'class' => ClassUtils::getClass($object),
                'id' => $object->getId(),
            ],
        ];
    }
}
