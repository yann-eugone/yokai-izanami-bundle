<?php

namespace Yokai\IzanamiBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityRepository;
use Iterator;
use RuntimeException;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class ObjectRepositoryUtil
{
    /**
     * @param ObjectRepository $repository
     *
     * @return Iterator
     */
    public function count(ObjectRepository $repository)
    {
        if ($repository instanceof EntityRepository) {
            return intval(
                $repository->createQueryBuilder('object')->select('COUNT(object)')->getQuery()->getSingleScalarResult()
            );
        }

        throw new RuntimeException('Not implemented yet');//todo
    }

    /**
     * @param ObjectRepository $repository
     *
     * @return Iterator
     */
    public function iterate(ObjectRepository $repository)
    {
        if ($repository instanceof EntityRepository) {
            return $this->getObjectFromIterator(
                $repository->createQueryBuilder('object')->getQuery()->iterate()
            );
        }

        throw new RuntimeException('Not implemented yet');//todo
    }

    /**
     * @param Iterator $iterator
     *
     * @return Iterator
     */
    private function getObjectFromIterator($iterator)
    {
        foreach ($iterator as $item) {
            yield $item[0];
        }
    }
}
