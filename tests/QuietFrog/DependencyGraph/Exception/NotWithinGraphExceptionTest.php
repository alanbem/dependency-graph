<?php

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */

namespace QuietFrog\DependencyGraph\Exception;

use QuietFrog\DependencyGraph\ObjectGraph;

/**
 * NotWithinGraphException tests
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class NotWithinGraphExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $object = new \stdClass();
        $graph  = new ObjectGraph();

        $exception = new NotWithinGraphException($object, $graph);

        $this->assertSame($object, $exception->getObject());
        $this->assertSame($graph, $exception->getGraph());
    }
}
