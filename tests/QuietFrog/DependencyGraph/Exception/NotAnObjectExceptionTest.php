<?php

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */

namespace QuietFrog\DependencyGraph\Exception;

/**
 * NotAnObjectException tests
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class NotAnObjectExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider parameters
     *
     * @param mixed $parameter
     */
    public function testException($parameter)
    {
        $exception = new NotAnObjectException($parameter);

        $this->assertSame($parameter, $exception->getParameter());
    }

    public function parameters()
    {
        return array(
            array(null),
            array(1),
            array(1.0),
            array(-100),
            array(''),
            array('Lorem ipsum dolor...'),
            array(true),
            array(false),
            array(array(null)),
            array(array(1)),
            array(array(1.0)),
            array(array(-100)),
            array(array('')),
            array(array('Lorem ipsum dolor...')),
            array(array(true)),
            array(array(false)),
            array(array()),
        );
    }
}
