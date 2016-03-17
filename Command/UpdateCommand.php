<?php

namespace Yokai\IzanamiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yokai\IzanamiBundle\Entity\ViolationRepository;
use Yokai\IzanamiBundle\Violation\ViolationConfigRegistry;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class UpdateCommand extends ContainerAwareCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('izanami:update')
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getViolationRepository();

        $progress = new ProgressBar($output, $repository->count());

        foreach ($this->getViolationConfigRegistry()->all() as $config) {
            $repository->updateConfig($config);

            $progress->advance(
                $repository->countOfType($config->getIdentifier())
            );
        }

        $progress->finish();
        $output->writeln('');
    }

    /**
     * @return ViolationRepository
     */
    private function getViolationRepository()
    {
        return $this->getContainer()->get('izanami.violation_repository');
    }

    /**
     * @return ViolationConfigRegistry
     */
    private function getViolationConfigRegistry()
    {
        return $this->getContainer()->get('izanami.violation_config_registry');
    }
}
