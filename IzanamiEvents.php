<?php

namespace Yokai\IzanamiBundle;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
final class IzanamiEvents
{
    /**
     * Private constructor. This class is not meant to be instantiated.
     */
    private function __construct()
    {
    }

    const REGISTER_OBSERVABLE = 'izanami.register_observable';

    const OBJECT_ANALYZED = 'izanami.object_analyzed';
}
