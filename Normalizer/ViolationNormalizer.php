<?php

namespace Yokai\IzanamiBundle\Normalizer;

use DateTime;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Yokai\IzanamiBundle\Entity\Violation;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class ViolationNormalizer implements NormalizerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $domain;

    /**
     * @param TranslatorInterface $translator
     * @param string              $domain
     */
    public function __construct(TranslatorInterface $translator, $domain = 'messages')
    {
        $this->translator = $translator;
        $this->domain = $domain;
    }

    /**
     * @inheritDoc
     */
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var $object Violation */

        return [
            'type' => $object->getType(),
            'severity' => $object->getSeverity(),
            'payload' => $object->getPayload(),
            'message' => [
                'message' => $this->getMessage($object),
                'pattern' => $object->getMessage(),
                'parameters' => $this->getMessageParameters($object),
            ],
            'created' => $object->getCreated()->format(DateTime::ISO8601),
        ];
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Violation;
    }

    /**
     * @param Violation $violation
     *
     * @return string
     */
    private function getMessage(Violation $violation)
    {
        return $this->translator->trans(
            $violation->getMessage(),
            $this->getMessageParameters($violation),
            $this->domain
        );
    }

    /**
     * @param Violation $violation
     *
     * @return string
     */
    private function getMessageParameters(Violation $violation)
    {
        $parameters = [];
        foreach ($violation->getPayload() as $key => $value) {
            $parameters[sprintf('{%s}', $key)] = $value;
        }

        return $parameters;
    }
}
