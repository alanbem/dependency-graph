<?php

namespace QuietFrog\DependencyGraph\Exception;

use QuietFrog\DependencyGraph\ObjectGraph;

class GraphNotWritableException extends \RuntimeException
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

        $message = 'Graph is already initialized and locked (read-only mode).';

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
