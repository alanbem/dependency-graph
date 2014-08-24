<?php

namespace QuietFrog\DependencyGraph\Exception;

use QuietFrog\DependencyGraph\Graph;

/**
 * GraphNotWritableException tests
 *
 * @author Alan Gabriel Bem <alan.bem@xsolve.pl>
 */
class GraphNotWritableExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $graph  = new Graph();

        $exception = new GraphNotWritableException($graph);

        $this->assertSame($graph, $exception->getGraph());
    }
}
