<?php

namespace QuietFrog\DependencyGraph\Exception;

use QuietFrog\DependencyGraph\Graph;

class GraphNotWritableException extends \RuntimeException
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

        $message = 'Graph is already initialized and locked (read-only mode).';

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
