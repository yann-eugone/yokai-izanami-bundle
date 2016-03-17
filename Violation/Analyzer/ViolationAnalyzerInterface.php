<?php

namespace Yokai\IzanamiBundle\Violation\Analyzer;

use Yokai\IzanamiBundle\Violation\ViolationContainerInterface;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
interface ViolationAnalyzerInterface
{
    /**
     * Whether or not the provided object is supported by this analyzer.
     *
     * @param object $object
     */
    public function supports($object);

    /**
     * Perform analyze of an object.
     *
     * @param ViolationContainerInterface $container
     */
    public function analyze(ViolationContainerInterface $container);
}
