<?php

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */

namespace QuietFrog\DependencyGraph\Exception;

use QuietFrog\DependencyGraph\ObjectGraph;

/**
 * NotWithinGraphException class
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class NotWithinGraphException extends \OutOfBoundsException
{
    /**
     * @var object
     */
    private $object;

    /**
     * @var ObjectGraph
     */
    private $graph;

    /**
     * Constructor.
     *
     * @param string $object
     * @param ObjectGraph $graph
     */
    public function __construct($object, ObjectGraph $graph)
    {
        $this->object = $object;
        $this->graph  = $graph;

        $message = 'Object $object is not within a $graph';

        parent::__construct($message);
    }

    /**
     * @return ObjectGraph
     */
    public function getGraph()
    {
        return $this->graph;
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }
} 
