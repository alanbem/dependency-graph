<?php

namespace QuietFrog\DependencyGraph;

use QuietFrog\DependencyGraph\Exception\CircularDependencyDetectedException;
use QuietFrog\DependencyGraph\Exception\GraphNotWritableException;
use QuietFrog\DependencyGraph\Exception\NotAnObjectException;
use QuietFrog\DependencyGraph\Exception\NotWithinGraphException;

class ObjectGraph
{
    /**
     * SplObjectStorage is buggy, so we stick to old plain array.
     *
     * @var Node[]
     */
    private $nodes = array();

    /**
     * @var bool
     */
    private $locked = false;

    /**
     * @param object $object
     * @return ObjectGraph
     * @throws Exception\GraphNotWritableException
     * @throws Exception\NotAnObjectException
     */
    public function add($object)
    {
        if ($this->locked) {
            throw new GraphNotWritableException($this);
        }

        $object = new Reference($object);

        $this->nodes[$object->getId()] = new Node($object);

        return $this;
    }

    /**
     * Given child operation depends on parent operation
     *
     * @param object $object
     * @param object $dependant
     * @return ObjectGraph
     *
     * @throws Exception\GraphNotWritableException
     * @throws Exception\NotWithinGraphException
     */
    public function addDependency($object, $dependant)
    {
        if ($this->locked) {
            throw new GraphNotWritableException($this);
        }

        $object = new Reference($object);
        $dependant = new Reference($dependant);

        if ($object->getId() === $dependant->getId()) {
            return $this;
        }

        if (false === array_key_exists($object->getId(), $this->nodes)) {
            throw new NotWithinGraphException($object->getObject(), $this);
        }

        if (false === array_key_exists($dependant->getId(), $this->nodes)) {
            throw new NotWithinGraphException($dependant->getObject(), $this);
        }

        $this->nodes[$dependant->getId()]->addDependency($object->getId());
        $this->nodes[$object->getId()]->addDependent($dependant->getId());

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
        $objects = array();

        foreach ($this->nodes as $node) {
            if (!$node->hasDependenciesLeft() && !$node->isStarted()) {
                $objects[] = $node->getReference()->getObject();
            }
        }

        return $objects;
    }

    /**
     * An operation that is marked as started, is not returned when ::getExecutableReferences() is called.
     * But dependency is not fulfilled, so other operations depending on the operation still have to wait.
     *
     * @param object $object
     *
     * @throws NotWithinGraphException
     */
    public function markAsResolving($object)
    {
        $object = new Reference($object);

        if (false === array_key_exists($object->getId(), $this->nodes)) {
            throw new NotWithinGraphException($object->getObject(), $this);
        }

        $this->initialize();
        $this->nodes[$object->getId()]->setStarted();
    }

    /**
     * @param object $object
     *
     * @throws NotWithinGraphException
     */
    public function markAsResolved($object)
    {
        $object = new Reference($object);

        if (false === array_key_exists($object->getId(), $this->nodes)) {
            throw new NotWithinGraphException($object->getObject(), $this);
        }

        $this->initialize();
        $node = $this->nodes[$object->getId()];
        foreach ($node->getDependents() as $dependentId) {
            $this->nodes[$dependentId]->decreaseDependencyCounter();
        }
        unset($this->nodes[$object->getId()]);
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
        if ($this->locked) {
            return;
        }
        $this->locked = true;

        $objects = $this->getUnresolvedDependencies();

        if (0 === count($objects)) {
            throw new CircularDependencyDetectedException($this);
        }

        foreach ($objects as $object) {
            $object = new Reference($object);
            $this->checkForDependencies($this->nodes[$object->getId()], array());
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

        foreach ($node->getDependents() as $dependantId) {
            $seenLists[] = $this->checkForDependencies($this->nodes[$dependantId], $seen);
        }

        return $seenLists;
    }

    /**
     * @param callable $callback
     * @return void
     *
     * @throws GraphNotWritableException
     * @throws \InvalidArgumentException
     */
    public function configure($callback)
    {
        if ($this->locked) {
            throw new GraphNotWritableException($this);
        }

        if (false === is_callable($callback)) {
            $message = '$callback is not callable.';
            throw new \InvalidArgumentException($message);
        }

        $parents = $this->nodes;
        $dependants = $this->nodes;

        foreach ($parents as $parentId => $parent) {
            foreach ($dependants as $dependantId => $dependant) {
                if ($parent->getId() === $dependant->getId()) {
                    continue;
                }

                if (true === $callback($this->nodes[$parentId]->getReferencedObject(), $this->nodes[$dependantId]->getReferencedObject())) {
                    $this->addDependency($parent, $dependant);
                }
            }
        }
    }

    /**
     * @param callable $callback
     * @return object[]
     *
     * @throws GraphNotWritableException
     * @throws \InvalidArgumentException
     */
    public function resolve($callback)
    {
        if (false === is_callable($callback)) {
            $message = '$callback is not callable.';
            throw new \InvalidArgumentException($message);
        }

        $this->initialize();

        foreach ($this->nodes as $parent) {
            $dependants = array_intersect_key($this->nodes, array_flip($parent->getDependents()));

            foreach ($dependants as $dependant) {
                $callback($parent->getReferencedObject(), $dependant->getReferencedObject());
            }
        }

        return $this->getUnresolvedDependencies();
    }
}
