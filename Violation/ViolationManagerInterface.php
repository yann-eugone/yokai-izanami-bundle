<?php

namespace Yokai\IzanamiBundle\Violation;

use Iterator;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
interface ViolationManagerInterface
{
    /**
     * Trigger analyze of a collection of objects.
     *
     * @param array|Iterator $objects
     */
    public function analyze($objects);
}
