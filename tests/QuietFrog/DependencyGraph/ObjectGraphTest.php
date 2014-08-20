<?php

namespace QuietFrog\DependencyGraph;

class ObjectGraphTest extends \PHPUnit_Framework_TestCase
{
    public function testDependencyGraphWithNoDefinedDependencies()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);
        $object3 = new TestObject(3);
        $object4 = new TestObject(4);

        $graph = new ObjectGraph();

        $graph->add($object1);
        $graph->add($object2);
        $graph->add($object3);
        $graph->add($object4);

        // 1.   1 2 3 4
        //      | | | |
        // 2.  [resolved]

        // 1.
        $this->assertTrue($graph->hasUnresolvedDependencies());
        $this->assertFalse($graph->isResolved());
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(4, $ops);
        $this->assertContains($object1, $ops);
        $this->assertContains($object2, $ops);
        $this->assertContains($object3, $ops);
        $this->assertContains($object4, $ops);

        $graph->markAsResolved($object1);
        $graph->markAsResolved($object2);
        $graph->markAsResolved($object3);
        $graph->markAsResolved($object4);

        // 2.
        $this->assertFalse($graph->hasUnresolvedDependencies());
        $this->assertTrue($graph->isResolved());
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(0, $ops);
    }

    public function testDependencyGraph()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);
        $object3 = new TestObject(3);
        $object4 = new TestObject(4);

        $graph = new ObjectGraph();

        $graph->add($object1);
        $graph->add($object2);
        $graph->add($object3);
        $graph->add($object4);

        // 1.      1
        //       /  \
        // 2.   2    3
        //       \  /
        // 3.     4
        //        |
        // 4. [resolved]

        $graph->addDependency($object1, $object2);
        $graph->addDependency($object1, $object3);
        $graph->addDependency($object2, $object4);
        $graph->addDependency($object3, $object4);

        // 1.
        $this->assertTrue($graph->hasUnresolvedDependencies());
        $this->assertFalse($graph->isResolved());
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(1, $ops);
        $this->assertContains($object1, $ops);

        $graph->markAsResolved($object1);

        // 2.
        $this->assertTrue($graph->hasUnresolvedDependencies());
        $this->assertFalse($graph->isResolved());
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(2, $ops);
        $this->assertContains($object2, $ops);
        $this->assertContains($object3, $ops);

        $graph->markAsResolved($object2);

        // 2.
        $this->assertTrue($graph->hasUnresolvedDependencies());
        $this->assertFalse($graph->isResolved());
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(1, $ops);
        $this->assertContains($object3, $ops);

        $graph->markAsResolved($object3);

        // 3.
        $this->assertTrue($graph->hasUnresolvedDependencies());
        $this->assertFalse($graph->isResolved());
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(1, $ops);
        $this->assertContains($object4, $ops);

        $graph->markAsResolved($object4);

        // 4.
        $this->assertFalse($graph->hasUnresolvedDependencies());
        $this->assertTrue($graph->isResolved());
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(0, $ops);
    }

    public function testGraphWithTwoEntryNodes()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);
        $object3 = new TestObject(3);
        $object4 = new TestObject(4);
        $object5 = new TestObject(5);
        $object6 = new TestObject(6);
        $object7 = new TestObject(7);
        $object8 = new TestObject(8);

        $graph = new ObjectGraph();

        $graph->add($object1);
        $graph->add($object2);
        $graph->add($object3);
        $graph->add($object4);
        $graph->add($object5);
        $graph->add($object6);
        $graph->add($object7);
        $graph->add($object8);

        // 1.     1     2
        //        |    / \
        // 2.     3   4   5
        //         \ /    |
        // 3.       6     7
        //          |
        // 4.       8
        //          |
        // 5.  [resolved]

        $graph->addDependency($object1, $object3);
        $graph->addDependency($object2, $object4);
        $graph->addDependency($object2, $object5);
        $graph->addDependency($object3, $object6);
        $graph->addDependency($object4, $object6);
        $graph->addDependency($object5, $object7);
        $graph->addDependency($object6, $object8);

        // 1.
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(2, $ops);
        $this->assertContains($object1, $ops);
        $this->assertContains($object2, $ops);

        $graph->markAsResolved($object1);
        $graph->markAsResolved($object2);

        // 2.
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(3, $ops);
        $this->assertContains($object3, $ops);
        $this->assertContains($object4, $ops);
        $this->assertContains($object5, $ops);

        $graph->markAsResolved($object3);
        $graph->markAsResolved($object4);
        $graph->markAsResolved($object5);

        // 3.
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(2, $ops);
        $this->assertContains($object6, $ops);
        $this->assertContains($object7, $ops);

        $graph->markAsResolved($object6);
        $graph->markAsResolved($object7);

        // 4.
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(1, $ops);
        $this->assertContains($object8, $ops);

        $graph->markAsResolved($object8);

        // 5.
        $this->assertFalse($graph->hasUnresolvedDependencies());
        $this->assertTrue($graph->isResolved());
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(0, $ops);
    }

    public function testAddingDependencyWhenSubjectAndDependantAreSameObject()
    {
        $object1 = new TestObject(1);

        $graph1 = new ObjectGraph();
        $graph2 = new ObjectGraph();

        $graph1->addDependency($object1, $object1);

        $this->assertEquals($graph1, $graph2); // $graph1 should ne still empty
    }

    public function testCycleDetectionWithoutEntryPoint()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);
        $object3 = new TestObject(3);

        $graph = new ObjectGraph();

        $graph->add($object1);
        $graph->add($object2);
        $graph->add($object3);

        //      1
        //    /  \
        //   2 -- 3

        $graph->addDependency($object1, $object2);
        $graph->addDependency($object2, $object3);
        $graph->addDependency($object3, $object1);

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\CircularDependencyDetectedException');

        $graph->getUnresolvedDependencies();
    }

    public function testCycleDetectionWithCycleWithinGraph()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);
        $object3 = new TestObject(3);
        $object4 = new TestObject(4);

        $graph = new ObjectGraph();

        $graph->add($object1);
        $graph->add($object2);
        $graph->add($object3);
        $graph->add($object4);

        //      1
        //      |
        //      2
        //    /  \
        //   3 -- 4

        $graph->addDependency($object1, $object2);
        $graph->addDependency($object2, $object3);
        $graph->addDependency($object3, $object4);
        $graph->addDependency($object4, $object2);

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\CircularDependencyDetectedException');

        $graph->getUnresolvedDependencies();
    }

    public function testMarkAsResolving()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);
        $object3 = new TestObject(3);
        $object4 = new TestObject(4);

        $graph = new ObjectGraph();

        $graph->add($object1);
        $graph->add($object2);
        $graph->add($object3);
        $graph->add($object4);

        // 1.      1
        //       /  \
        // 2.   2    3
        //       \  /
        // 3.     4
        //        |
        // 4. [resolved]

        $graph->addDependency($object1, $object2);
        $graph->addDependency($object1, $object3);
        $graph->addDependency($object2, $object4);
        $graph->addDependency($object3, $object4);

        // 1.
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(1, $ops);
        $this->assertContains($object1, $ops);

        $graph->markAsResolving($object1);

        // 1.
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(0, $ops);

        $graph->markAsResolved($object1);

        // 2.
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(2, $ops);
        $this->assertContains($object2, $ops);
        $this->assertContains($object3, $ops);

        $graph->markAsResolving($object2);

        // 2.
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(1, $ops);
        $this->assertContains($object3, $ops);

        $graph->markAsResolved($object3);

        // 2.
        $this->assertFalse($graph->hasUnresolvedDependencies()); // $object2 is during resolving, so graph is waiting...
        $this->assertFalse($graph->isResolved());
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(0, $ops);

        $graph->markAsResolved($object2); // but when we resolve $object2

        // 3.
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(1, $ops);
        $this->assertContains($object4, $ops); // new dependencies come along

        $graph->markAsResolved($object4);

        // 4.
        $this->assertFalse($graph->hasUnresolvedDependencies());
        $this->assertTrue($graph->isResolved());
        $ops = $graph->getUnresolvedDependencies();
        $this->assertCount(0, $ops);
    }

    public function testAddingObjectAfterGraphInitialization()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);
        $object3 = new TestObject(3);

        $graph = new ObjectGraph();

        $graph->add($object1);
        $graph->add($object2);

        $graph->addDependency($object1, $object2);

        $graph->getUnresolvedDependencies();

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\GraphNotWritableException');

        $graph->add($object3);
    }

    public function testAddingDependenciesAfterGraphInitialization()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);
        $object3 = new TestObject(3);
        $object4 = new TestObject(4);

        $graph = new ObjectGraph();

        $graph->add($object1);
        $graph->add($object2);
        $graph->add($object3);
        $graph->add($object4);

        $graph->addDependency($object1, $object2);
        $graph->addDependency($object1, $object3);
        $graph->addDependency($object2, $object4);
        $graph->addDependency($object3, $object4);

        $graph->getUnresolvedDependencies();

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\GraphNotWritableException');

        $graph->addDependency($object1, $object4);
    }

    public function testAddingDependencyForObjectNotWithinAGraph()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);

        $graph = new ObjectGraph();

        $graph->add($object1);

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\NotWithinGraphException');

        $graph->addDependency($object2, $object1);
    }

    public function testAddingDependencyWithDependantNotWithinAGraph()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);

        $graph = new ObjectGraph();

        $graph->add($object1);

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\NotWithinGraphException');

        $graph->addDependency($object1, $object2);
    }

    public function testMarkingAsResolvingAnObjectNotWithinAGraph()
    {
        $object1 = new TestObject(1);

        $graph = new ObjectGraph();

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\NotWithinGraphException');

        $graph->markAsResolving($object1);
    }

    public function testMarkingAsResolvedAnObjectNotWithinAGraph()
    {
        $object1 = new TestObject(1);

        $graph = new ObjectGraph();

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\NotWithinGraphException');

        $graph->markAsResolved($object1);
    }

    /**
     * @dataProvider getNonObjects
     *
     * @param $nonObject
     */
    public function testAddingNonObject($nonObject)
    {
        $graph = new ObjectGraph();

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\NotAnObjectException');

        $graph->add($nonObject);
    }

    /**
     * @dataProvider getNonObjects
     *
     * @param $nonObject
     */
    public function testAddingDependencyForNonObject($nonObject)
    {
        $graph = new ObjectGraph();

        $object = new TestObject(1);

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\NotAnObjectException');

        $graph->addDependency($nonObject, $object);
    }
    /**
     * @dataProvider getNonObjects
     *
     * @param $nonObject
     */
    public function testAddingNonObjectAsDependency($nonObject)
    {
        $graph = new ObjectGraph();

        $object = new TestObject(1);

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\NotAnObjectException');

        $graph->addDependency($object, $nonObject);
    }

    /**
     * @dataProvider getNonObjects
     *
     * @param $nonObject
     */
    public function testMarkingAsResolvedNonObject($nonObject)
    {
        $graph = new ObjectGraph();

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\NotAnObjectException');

        $graph->markAsResolved($nonObject);
    }

    /**
     * @dataProvider getNonObjects
     *
     * @param $nonObject
     */
    public function testMarkingAsResolvingNonObject($nonObject)
    {
        $graph = new ObjectGraph();

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\NotAnObjectException');

        $graph->markAsResolving($nonObject);
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

/**
 * Just a simple stub to make tests easier to read and debugging.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class TestObject
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
}
