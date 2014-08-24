<?php

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */

namespace QuietFrog\DependencyGraph\Exception;

use QuietFrog\DependencyGraph\Graph;

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
     * @var Graph
     */
    private $graph;

    /**
     * Constructor.
     *
     * @param string $object
     * @param Graph $graph
     */
    public function __construct($object, Graph $graph)
    {
        $this->object = $object;
        $this->graph  = $graph;

        $message = 'Object $object is not within a $graph';

        parent::__construct($message);
    }

    /**
     * @return Graph
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
