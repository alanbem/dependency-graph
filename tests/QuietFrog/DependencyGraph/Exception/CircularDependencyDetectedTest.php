<?php

namespace QuietFrog\DependencyGraph\Exception;

use QuietFrog\DependencyGraph\ObjectGraph;

/**
 * CircularDependencyDetectedException tests
 *
 * @author Alan Gabriel Bem <alan.bem@xsolve.pl>
 */
class CircularDependencyDetectedExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $graph  = new ObjectGraph();

        $exception = new CircularDependencyDetectedException($graph);

        $this->assertSame($graph, $exception->getGraph());
    }
}
