<?php

/**
 * @author Alan Gabriel Bem <alan.bem@xsolve.pl>
 */

namespace QuietFrog\DependencyGraph;

/**
 * NodeTest class
 *
 * @author Alan Gabriel Bem <alan.bem@xsolve.pl>
 */
class NodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reference|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reference;

    /**
     * @var Node
     */
    private $node;

    public function setUp()
    {
        $this->reference = $this
            ->getMockBuilder('QuietFrog\DependencyGraph\Reference')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'getObject'))
            ->getMock()
        ;

        $this->node = new Node($this->reference);
    }

    public function testNode()
    {
        $this->reference = $this
            ->getMockBuilder('QuietFrog\DependencyGraph\Reference')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', 'getObject'))
            ->getMock()
        ;

        $this->node = new Node($this->reference);

        $this->assertSame($this->reference, $this->node->getReference());

        $this->reference
            ->expects($this->once())
            ->method('getId')
            ->willReturn('object_id_1')
        ;

        $this->assertSame('object_id_1', $this->node->getId());

        $object = new \stdClass();

        $this->reference
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($object)
        ;

        $this->assertSame($object, $this->node->getReferencedObject());
    }

    public function testIsStarted()
    {
        $this->assertFalse($this->node->isStarted()); // initial

        $this->node->setStarted(true);

        $this->assertTrue($this->node->isStarted());

        $this->node->setStarted(false);

        $this->assertFalse($this->node->isStarted());
    }

    public function testDependencyCounter()
    {
        $this->assertFalse($this->node->hasDependenciesLeft());

        $this->node->addDependency();

        $this->assertTrue($this->node->hasDependenciesLeft());

        $this->node->decreaseDependencyCounter();

        $this->assertFalse($this->node->hasDependenciesLeft());
    }

    public function testDependents()
    {
        $this->assertFalse($this->node->hasDependents());

        $dependents = $this->node->getDependents();

        $this->assertInternalType('array', $dependents);
        $this->assertCount(0, $dependents);

        $this->node->addDependent('id_1');

        $this->assertTrue($this->node->hasDependents());

        $dependents = $this->node->getDependents();

        $this->assertInternalType('array', $dependents);
        $this->assertCount(1, $dependents);
        $this->assertContains('id_1', $dependents);
    }
} 
