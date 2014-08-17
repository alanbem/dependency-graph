<?php

namespace QuietFrog\DependencyGraph;

use QuietFrog\DependencyGraph\Exception\CircularDependencyDetectedException;
use QuietFrog\DependencyGraph\Exception\GraphNotWritableException;
use QuietFrog\DependencyGraph\Exception\NotAnObjectException;
use QuietFrog\DependencyGraph\Exception\NotWithinGraphException;

class ObjectGraph
{
    /**
     * @var object[]
     */
    private $objects = array();

    /**
     * SplObjectStorage is buggy, so we stick to old plain array.
     *
     * @var Node[]
     */
    private $nodes = array();

    /**
     * @var bool
     */
    private $graphInitialized = false;

    /**
     * @param object $object
     * @return ObjectGraph
     * @throws Exception\GraphNotWritableException
     */
    public function add($object)
    {
        if ($this->graphInitialized) {
            throw new GraphNotWritableException($this);
        }

        if (false === is_object($object)) {
            throw new NotAnObjectException($object);
        }

        $id = spl_object_hash($object);
        $this->objects[$id] = $object;
        $reference = new Reference($id);

        $this->nodes[$reference->getId()] = new Node($reference);

        return $this;
    }

    /**
     * Given child operation depends on parent operation
     *
     * @param object $object
     * @param object $dependant
     * @return ObjectGraph
     * @throws Exception\GraphNotWritableException
     */
    public function addDependency($object, $dependant)
    {
        if ($this->graphInitialized) {
            throw new GraphNotWritableException($this);
        }

        if (false === is_object($object)) {
            throw new NotAnObjectException($object);
        }

        if (false === is_object($dependant)) {
            throw new NotAnObjectException($object);
        }

        if ($object === $dependant) {
            return $this;
        }

        $objectId = spl_object_hash($object);

        if (false === array_key_exists($objectId, $this->nodes)) {
            throw new NotWithinGraphException($object, $this);
        }

        $dependantId = spl_object_hash($dependant);

        if (false === array_key_exists($dependantId, $this->nodes)) {
            throw new NotWithinGraphException($dependant, $this);
        }

        $this->nodes[$dependantId]->addDependency($objectId);
        $this->nodes[$objectId]->addDependent($dependantId);

        return $this;
    }

    /**
     * @return boolean
     */
    public function hasUnresolvedDependencies()
    {
        return (boolean) count($this->getUnresolvedDependencies());
    }

    /**
     * @return array
     */
    public function getUnresolvedDependencies()
    {
        $this->initialize();
        $list = array();

        foreach ($this->nodes as $node) {
            if (!$node->hasDependenciesLeft() && !$node->isStarted()) {
                $list[] = $node->getReference()->getId();
            }
        }

        return array_intersect_key($this->objects, array_flip($list));
    }

    /**
     * An operation that is marked as started, is not returned when ::getExecutableReferences() is called.
     * But dependency is not fulfilled, so other operations depending on the operation still have to wait.
     *
     * @param object $object
     */
    public function markAsResolving($object)
    {
        if (false === is_object($object)) {
            throw new NotAnObjectException($object);
        }

        $id = spl_object_hash($object);

        if (false === array_key_exists($id, $this->nodes)) {
            throw new NotWithinGraphException($object, $this);
        }

        $this->initialize();
        $this->nodes[$id]->setStarted();
    }

    /**
     * @param object $object
     */
    public function markAsResolved($object)
    {
        if (false === is_object($object)) {
            throw new NotAnObjectException($object);
        }

        $id = spl_object_hash($object);

        if (false === array_key_exists($id, $this->nodes)) {
            throw new NotWithinGraphException($object, $this);
        }

        $this->initialize();
        $node = $this->nodes[$id];
        foreach ($node->getDependents() as $dependent) {
            $this->nodes[$dependent]->decreaseDependencyCounter();
        }
        unset($this->nodes[$id]);
    }

    /**
     * Returns true if all operations are marked as executed
     *
     * @return bool
     */
    public function isResolved()
    {
        $this->initialize();
        return count($this->nodes) < 1;
    }

    private function initialize()
    {
        if ($this->graphInitialized) {
            return;
        }
        $this->graphInitialized = true;

        $ops = $this->getUnresolvedDependencies();
        if (empty($ops)) {
            throw new CircularDependencyDetectedException($this);
        }
        foreach ($ops as $op) {
            $id = spl_object_hash($op);
            $this->checkForDependencies($this->nodes[$id], array());
        }
    }

    private function checkForDependencies(Node $node, array $seen)
    {
        if (in_array($node->getId(), $seen)) {
            throw new CircularDependencyDetectedException($this);
        }
        $seen[] = $node->getId();

        if (!$node->hasDependents()) {
            return $seen;
        }

        $seenLists = array();

        foreach ($node->getDependents() as $dep) {
            $seenLists[] = $this->checkForDependencies($this->nodes[$dep], $seen);
        }

        return $seenLists;
    }
}
