<?php

namespace QuietFrog\DependencyGraph\Exception;

use QuietFrog\DependencyGraph\ObjectGraph;

class CircularDependencyDetectedException extends \RuntimeException
{
    /**
     * @var ObjectGraph
     */
    private $graph;

    /**
     * @param ObjectGraph $graph
     */
    public function __construct(ObjectGraph $graph)
    {
        $this->graph = $graph;

        $message = 'Cannot find an entry point to the graph. You have built a cycle.';

        parent::__construct($message);
    }

    /**
     * @return ObjectGraph
     */
    public function getGraph()
    {
        return $this->graph;
    }
}
