<?php

namespace QuietFrog\DependencyGraph\Exception;

use QuietFrog\DependencyGraph\Graph;

/**
 * CircularDependencyDetectedException tests
 *
 * @author Alan Gabriel Bem <alan.bem@xsolve.pl>
 */
class CircularDependencyDetectedExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $graph  = new Graph();

        $exception = new CircularDependencyDetectedException($graph);

        $this->assertSame($graph, $exception->getGraph());
    }
}
