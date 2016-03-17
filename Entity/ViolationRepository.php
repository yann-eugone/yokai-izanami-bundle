<?php

namespace Yokai\IzanamiBundle\Entity;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityRepository;
use Yokai\IzanamiBundle\Violation\ViolationConfig;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class ViolationRepository extends EntityRepository
{
    /**
     * Get registered violations for an object class and id.
     *
     * @param string $class
     * @param string $id
     *
     * @return Violation[]
     */
    public function getForClassAndId($class, $id)
    {
        return $this->findBy(
            [
                'objectClass' => $class,
                'objectId' => $id,
            ]
        );
    }

    /**
     * Get registered violations for a given object.
     *
     * @param Observable $object
     *
     * @return Violation[]
     */
    public function getForObject($object)
    {
        if (!$object instanceof Observable) {
            return [];
        }

        return $this->getForClassAndId(ClassUtils::getClass($object), $object->getId());
    }

    /**
     * Remove registered violations for a given object.
     *
     * @param Observable $object
     */
    public function removeForObject($object)
    {
        $violations = $this->getForObject($object);
        if (0 === count($violations)) {
            return;
        }

        $manager = $this->getEntityManager();

        foreach ($violations as $violation) {
            $manager->remove($violation);
        }

        $manager->flush($violations);
    }

    /**
     * Update violations in database with fresh configuration.
     *
     * @param ViolationConfig $config
     */
    public function updateConfig(ViolationConfig $config)
    {
        $builder = $this->getEntityManager()->createQueryBuilder();
        $builder->update($this->_entityName, 'v')
            ->set('v.message', ':message')
            ->set('v.severity', ':severity')
            ->where('v.type = :type')
            ->setParameters(
                [
                    'message' => $config->getMessage(),
                    'severity' => $config->getSeverity(),
                    'type' => $config->getIdentifier(),
                ]
            )
        ;

        $builder->getQuery()->execute();
    }

    /**
     * Get count violations in database for a type.
     *
     * @param string $type
     *
     * @return int
     */
    public function countOfType($type)
    {
        $builder = $this->createQueryBuilder('v');
        $builder->select('COUNT(v)')
            ->where('v.type = :type')
            ->setParameters(
                [
                    'type' => $type,
                ]
            )
        ;

        return intval($builder->getQuery()->getSingleScalarResult());
    }

    /**
     * Get count violations in database.
     *
     * @return int
     */
    public function count()
    {
        $builder = $this->createQueryBuilder('v');
        $builder->select('COUNT(v)');

        return intval($builder->getQuery()->getSingleScalarResult());
    }
}
