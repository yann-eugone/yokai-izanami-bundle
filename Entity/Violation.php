<?php

namespace Yokai\IzanamiBundle\Entity;

use DateTime;
use Doctrine\Common\Util\ClassUtils;
use Yokai\IzanamiBundle\Violation\ViolationConfig;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class Violation
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $severity;

    /**
     * @var string
     */
    private $objectClass;

    /**
     * @var string
     */
    private $objectId;

    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $payload = [];

    /**
     * @var DateTime
     */
    private $created;

    /**
     * @var DateTime|null
     */
    private $updated;

    /**
     * @param string     $type
     * @param int        $severity
     * @param Observable $object
     * @param string     $message
     * @param array      $payload
     */
    public function __construct($type, $severity, Observable $object, $message, array $payload)
    {
        $this->type = $type;
        $this->severity = $severity;
        $this->objectClass = ClassUtils::getClass($object);
        $this->objectId = $object->getId();
        $this->message = $message;
        $this->payload = $payload;
        $this->created = new DateTime('now');
    }

    /**
     * @param array $payload
     */
    public function update(array $payload = [])
    {
        if ($this->payload === $payload) {
            return;
        }

        $this->payload = $payload;
        $this->updated = new DateTime('now');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
    public function getObjectClass()
    {
        return $this->objectClass;
    }

    /**
     * @return string
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
