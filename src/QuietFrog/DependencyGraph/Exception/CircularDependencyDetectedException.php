<?php

namespace QuietFrog\DependencyGraph\Exception;

use QuietFrog\DependencyGraph\Graph;

class CircularDependencyDetectedException extends \RuntimeException
{
    /**
     * @var Graph
     */
    private $graph;

    /**
     * @param Graph $graph
     */
    public function __construct(Graph $graph)
    {
        $this->graph = $graph;

        $message = 'Cannot find an entry point to the graph. You have built a cycle.';

        parent::__construct($message);
    }

    /**
     * @return Graph
     */
    public function getGraph()
    {
        return $this->graph;
    }
}
