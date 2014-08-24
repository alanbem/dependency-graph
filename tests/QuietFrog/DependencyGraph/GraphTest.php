<?php

namespace QuietFrog\DependencyGraph;

class GraphTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyGraph()
    {
        $graph = new Graph();

        $this->assertTrue($graph->isResolved());

        $unresolved = $graph->getUnresolvedDependencies();

        $this->assertInternalType('array', $unresolved);
        $this->assertCount(0, $unresolved);
    }

    public function testDependencyGraphWithNoDefinedDependencies()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);
        $object3 = new TestObject(3);
        $object4 = new TestObject(4);

        $graph = new Graph();

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

        $graph = new Graph();

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

        $graph = new Graph();

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

        $graph1 = new Graph();
        $graph2 = new Graph();

        $graph1->addDependency($object1, $object1);

        $this->assertEquals($graph1, $graph2); // $graph1 should ne still empty
    }

    public function testCycleDetectionWithoutEntryPoint()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);
        $object3 = new TestObject(3);

        $graph = new Graph();

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

        $graph = new Graph();

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

        $graph = new Graph();

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

        $graph = new Graph();

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

        $graph = new Graph();

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

        $graph = new Graph();

        $graph->add($object1);

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\NotWithinGraphException');

        $graph->addDependency($object2, $object1);
    }

    public function testAddingDependencyWithDependantNotWithinAGraph()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);

        $graph = new Graph();

        $graph->add($object1);

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\NotWithinGraphException');

        $graph->addDependency($object1, $object2);
    }

    public function testMarkingAsResolvingAnObjectNotWithinAGraph()
    {
        $object1 = new TestObject(1);

        $graph = new Graph();

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\NotWithinGraphException');

        $graph->markAsResolving($object1);
    }

    public function testMarkingAsResolvedAnObjectNotWithinAGraph()
    {
        $object1 = new TestObject(1);

        $graph = new Graph();

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
        $graph = new Graph();

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
        $graph = new Graph();

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
        $graph = new Graph();

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
        $graph = new Graph();

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
        $graph = new Graph();

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

    public function testResolveDependencyGraphWithNoDefinedDependencies()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);
        $object3 = new TestObject(3);
        $object4 = new TestObject(4);

        $graph = new Graph();

        $graph->add($object1);
        $graph->add($object2);
        $graph->add($object3);
        $graph->add($object4);

        // 1.   1 2 3 4
        //      | | | |
        // 2.  [resolved]

        $resolver = $this->getMock('stdClass', array('resolve'));

        $resolver
            ->expects($this->never())
            ->method('resolve')
        ;

        $graph->resolve(array($resolver, 'resolve'));
    }

    public function testResolveDependencyGraph()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);
        $object3 = new TestObject(3);
        $object4 = new TestObject(4);

        $graph = new Graph();

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

        $resolver = $this->getMock('stdClass', array('resolve'));

        $resolver
            ->expects($this->at(0))
            ->method('resolve')
            ->with($object1, $object2)
        ;

        $resolver
            ->expects($this->at(1))
            ->method('resolve')
            ->with($object1, $object3)
        ;

        $resolver
            ->expects($this->at(2))
            ->method('resolve')
            ->with($object2, $object4)
        ;

        $resolver
            ->expects($this->at(3))
            ->method('resolve')
            ->with($object3, $object4)
        ;


        $graph->resolve(array($resolver, 'resolve'));
    }


    public function testResolveDependencyGraphWithTwoEntryNodes()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);
        $object3 = new TestObject(3);
        $object4 = new TestObject(4);
        $object5 = new TestObject(5);
        $object6 = new TestObject(6);
        $object7 = new TestObject(7);
        $object8 = new TestObject(8);

        $graph = new Graph();

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

        $resolver = $this->getMock('stdClass', array('resolve'));

        $resolver
            ->expects($this->at(0))
            ->method('resolve')
            ->with($object1, $object3)
        ;

        $resolver
            ->expects($this->at(1))
            ->method('resolve')
            ->with($object2, $object4)
        ;

        $resolver
            ->expects($this->at(2))
            ->method('resolve')
            ->with($object2, $object5)
        ;

        $resolver
            ->expects($this->at(3))
            ->method('resolve')
            ->with($object3, $object6)
        ;

        $resolver
            ->expects($this->at(4))
            ->method('resolve')
            ->with($object4, $object6)
        ;

        $resolver
            ->expects($this->at(5))
            ->method('resolve')
            ->with($object5, $object7)
        ;

        $resolver
            ->expects($this->at(6))
            ->method('resolve')
            ->with($object6, $object8)
        ;

        $graph->resolve(array($resolver, 'resolve'));
    }

    /**
     * @dataProvider getNonCallables
     *
     * @param mixed $nonCallable
     */
    public function testResolveNonCallable($nonCallable)
    {
        $graph = new Graph();

        $this->setExpectedException('InvalidArgumentException');

        $graph->resolve($nonCallable);
    }

    public function getNonCallables()
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
            array(new \stdClass()),
        );
    }

    /**
     * @dataProvider getNonCallables
     *
     * @param mixed $nonCallable
     */
    public function testConfiguringNonCallable($nonCallable)
    {
        $graph = new Graph();

        $this->setExpectedException('InvalidArgumentException');

        $graph->configure($nonCallable);
    }

    public function testConfiguringEmptyGraph()
    {
        $graph = new Graph();

        $configurator = $this->getMock('stdClass', array('configure'));

        $configurator
            ->expects($this->never())
            ->method('configure')
        ;

        $graph->configure(array($configurator, 'configure'));
    }

    public function testConfiguringLockedGraph()
    {
        $graph = new Graph();

        $configurator = $this->getMock('stdClass', array('configure'));

        $configurator
            ->expects($this->never())
            ->method('configure')
        ;

        $graph->isResolved(); // lock is performed here

        $this->setExpectedException('QuietFrog\DependencyGraph\Exception\GraphNotWritableException');

        $graph->configure(array($configurator, 'configure'));
    }

    public function testConfiguringGraphWithOneObject()
    {
        $object1 = new TestObject(1);

        $graph = new Graph();

        $graph->add($object1);

        $configurator = $this->getMock('stdClass', array('configure'));

        $configurator
            ->expects($this->never())
            ->method('configure')
        ;

        $graph->configure(array($configurator, 'configure'));
    }

    /**
     * Tested graph:
     *
     *       1
     *     /  \
     *    2    3
     *     \  /
     *      4
     *
     */
    public function testConfiguringGraph()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);
        $object3 = new TestObject(3);
        $object4 = new TestObject(4);

        $graph = new Graph();

        $graph->add($object1);
        $graph->add($object2);
        $graph->add($object3);
        $graph->add($object4);

        $configurator = $this->getMock('stdClass', array('configure'));

        $configurator
            ->expects($this->at(0))
            ->method('configure')
            ->with($object1, $object2)
            ->willReturn(true);
        ;

        $configurator
            ->expects($this->at(1))
            ->method('configure')
            ->with($object1, $object3)
            ->willReturn(true);
        ;

        $configurator
            ->expects($this->at(5))
            ->method('configure')
            ->with($object2, $object4)
            ->willReturn(true);
        ;

        $configurator
            ->expects($this->at(8))
            ->method('configure')
            ->with($object3, $object4)
            ->willReturn(true);
        ;

        // any other "configure" call results in false
        $configurator
            ->expects($this->any())
            ->method('configure')
            ->willReturn(false);
        ;

        $graph->configure(array($configurator, 'configure'));


        $expected = new Graph();
        $expected->add($object1);
        $expected->add($object2);
        $expected->add($object3);
        $expected->add($object4);
        $expected->addDependency($object1, $object2);
        $expected->addDependency($object1, $object3);
        $expected->addDependency($object2, $object4);
        $expected->addDependency($object3, $object4);

        $this->assertEquals($expected, $graph);
    }

    /**
     * Tested graph:
     *
     *    1     2
     *    |    / \
     *    3   4   5
     *     \ /    |
     *      6     7
     *      |
     *      8
     *
     */
    public function testConfiguringGraphWithTwoEntryNodes()
    {
        $object1 = new TestObject(1);
        $object2 = new TestObject(2);
        $object3 = new TestObject(3);
        $object4 = new TestObject(4);
        $object5 = new TestObject(5);
        $object6 = new TestObject(6);
        $object7 = new TestObject(7);
        $object8 = new TestObject(8);

        $graph = new Graph();

        $graph->add($object1);
        $graph->add($object2);
        $graph->add($object3);
        $graph->add($object4);
        $graph->add($object5);
        $graph->add($object6);
        $graph->add($object7);
        $graph->add($object8);

        $configurator = $this->getMock('stdClass', array('configure'));

        $configurator
            ->expects($this->at(1))
            ->method('configure')
            ->with($object1, $object3)
            ->willReturn(true);
        ;

        $configurator
            ->expects($this->at(9))
            ->method('configure')
            ->with($object2, $object4)
            ->willReturn(true);
        ;

        $configurator
            ->expects($this->at(10))
            ->method('configure')
            ->with($object2, $object5)
            ->willReturn(true);
        ;

        $configurator
            ->expects($this->at(18))
            ->method('configure')
            ->with($object3, $object6)
            ->willReturn(true);
        ;

        $configurator
            ->expects($this->at(25))
            ->method('configure')
            ->with($object4, $object6)
            ->willReturn(true);
        ;

        $configurator
            ->expects($this->at(33))
            ->method('configure')
            ->with($object5, $object7)
            ->willReturn(true);
        ;

        $configurator
            ->expects($this->at(41))
            ->method('configure')
            ->with($object6, $object8)
            ->willReturn(true);
        ;

        // any other "configure" call results in false
        $configurator
            ->expects($this->any())
            ->method('configure')
            ->willReturn(false);
        ;

        $graph->configure(array($configurator, 'configure'));


        $expected = new Graph();
        $expected->add($object1);
        $expected->add($object2);
        $expected->add($object3);
        $expected->add($object4);
        $expected->add($object5);
        $expected->add($object6);
        $expected->add($object7);
        $expected->add($object8);
        $expected->addDependency($object1, $object3);
        $expected->addDependency($object2, $object4);
        $expected->addDependency($object2, $object5);
        $expected->addDependency($object3, $object6);
        $expected->addDependency($object4, $object6);
        $expected->addDependency($object5, $object7);
        $expected->addDependency($object6, $object8);

        $this->assertEquals($expected, $graph);
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
