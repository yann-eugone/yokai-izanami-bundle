<?php

namespace Yokai\IzanamiBundle\Violation;

use Yokai\IzanamiBundle\Violation\Analyzer\ViolationAnalyzerInterface;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class ViolationConfig
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var int
     */
    private $severity;

    /**
     * @var string
     */
    private $message;

    /**
     * @var ViolationAnalyzerInterface
     */
    private $analyzer;

    /**
     * @param string                     $identifier
     * @param int                        $severity
     * @param string                     $message
     * @param ViolationAnalyzerInterface $analyzer
     */
    public function __construct($identifier, $severity, $message, ViolationAnalyzerInterface $analyzer)
    {
        $this->identifier = $identifier;
        $this->severity = $severity;
        $this->message = $message;
        $this->analyzer = $analyzer;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return int
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return ViolationAnalyzerInterface
     */
    public function getAnalyzer()
    {
        return $this->analyzer;
    }
}
