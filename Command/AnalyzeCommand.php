<?php

namespace Yokai\IzanamiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Yokai\IzanamiBundle\Doctrine\ObjectRepositoryUtil;
use Yokai\IzanamiBundle\Doctrine\ServiceLocator;
use Yokai\IzanamiBundle\Entity\Observable;
use Yokai\IzanamiBundle\Event\ObjectAnalyzedEvent;
use Yokai\IzanamiBundle\IzanamiEvents;
use Yokai\IzanamiBundle\Violation\ViolationManagerInterface;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class AnalyzeCommand extends ContainerAwareCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('izanami:analyze')
            ->addArgument('entities', InputArgument::IS_ARRAY, 'List of observable entities to analyze.')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $violationManager = $this->getViolationManager();
        $doctrineServiceLocator = $this->getDoctrineServiceLocator();
        $doctrineRepositoryUtil = $this->getDoctrineRepositoryUtil();
        $dispatcher = $this->getDispatcher();

        foreach ($input->getArgument('entities') as $entityName) {
            $repository = $doctrineServiceLocator->getRepository($entityName);
            $metadata = $doctrineServiceLocator->getMetadata($entityName);

            if (!$metadata->getReflectionClass()->implementsInterface(Observable::class)) {
                $output->writeln(
                    sprintf(
                        '<error>Class <comment>%s</comment> must implement <comment>%s</comment> interface.</error>',
                        $entityName,
                        Observable::class
                    )
                );

                continue;
            }

            $count = $doctrineRepositoryUtil->count($repository);

            $output->writeln(
                sprintf(
                    '<info>Attempting to analyze <comment>%d</comment> objects of class <comment>%s</comment>.</info>',
                    $count,
                    $entityName
                )
            );

            $inserted = 0;
            $removed = 0;

            $progress = new ProgressBar($output, $count);
            $dispatcher->addListener(
                IzanamiEvents::OBJECT_ANALYZED,
                function (ObjectAnalyzedEvent $event) use ($progress, &$inserted, &$removed) {
                    $progress->advance();

                    $inserted += count($event->getViolationsInserted());
                    $removed += count($event->getViolationRemoved());
                }
            );

            $violationManager->analyze(
                $doctrineRepositoryUtil->iterate($repository)
            );

            $progress->finish();
            $output->writeln('');

            $output->writeln(
                sprintf(
                    ' <info>Created : <comment>%d</comment>. Removed :<comment>%d</comment>.</info>',
                    $inserted,
                    $removed
                )
            );
        }
    }

    /**
     * @return ViolationManagerInterface
     */
    private function getViolationManager()
    {
        return $this->getContainer()->get('izanami.violation_manager');
    }

    /**
     * @return ServiceLocator
     */
    private function getDoctrineServiceLocator()
    {
        return $this->getContainer()->get('izanami.doctrine_service_locator');
    }

    /**
     * @return ObjectRepositoryUtil
     */
    private function getDoctrineRepositoryUtil()
    {
        return $this->getContainer()->get('izanami.doctrine_object_repository_util');
    }

    /**
     * @return EventDispatcherInterface
     */
    private function getDispatcher()
    {
        return $this->getContainer()->get('event_dispatcher');
    }
}
