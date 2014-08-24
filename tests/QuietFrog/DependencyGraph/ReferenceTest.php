<?php

/**
 * @author Alan Gabriel Bem <alan.bem@xsolve.pl>
 */

namespace QuietFrog\DependencyGraph;

/**
 * ReferenceTest class
 *
 * @author Alan Gabriel Bem <alan.bem@xsolve.pl>
 */
class ReferenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getNonObjects
     *
     * @param mixed $nonObject
     */
    public function testNotAnObject($nonObject)
    {
        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\NotAnObjectException');

        $reference = new Reference($nonObject);
    }

    public function testReference()
    {
        $object = new \stdClass();

        $reference = new Reference($object);

        $this->assertSame($object, $reference->getObject());
        $this->assertSame(spl_object_hash($object), $reference->getId());
    }

    public function getNonObjects()
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
