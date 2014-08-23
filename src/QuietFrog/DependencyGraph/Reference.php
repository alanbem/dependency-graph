<?php

namespace QuietFrog\DependencyGraph;

use QuietFrog\DependencyGraph\Exception\NotAnObjectException;

class Reference
{
    /**
     * @var object
     */
    private $object;

    /**
     * @param object $object
     *
     * @throws NotAnObjectException
     */
    public function __construct($object)
    {
        if (false === is_object($object)) {
            throw new NotAnObjectException($object);
        }

        $this->object = $object;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return spl_object_hash($this->object);
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }
}
