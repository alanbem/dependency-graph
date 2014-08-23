<?php

namespace QuietFrog\DependencyGraph;

class Node
{
    /**
     * @var Reference
     */
    private $reference;

    /**
     * @var array
     */
    private $dependents = array();

    /**
     * @var int
     */
    private $dependencyCounter = 0;

    /**
     * @var bool
     */
    private $started = false;

    /**
     * @param Reference $reference
     */
    public function __construct(Reference $reference)
    {
        $this->reference = $reference;
    }

    public function addDependency()
    {
        $this->dependencyCounter++;
    }

    public function addDependent($id)
    {
        $this->dependents[] = $id;
    }

    public function decreaseDependencyCounter()
    {
        $this->dependencyCounter--;
    }

    public function hasDependenciesLeft()
    {
        return $this->dependencyCounter > 0;
    }

    public function hasDependents()
    {
        return count($this->dependents) > 0;
    }

    public function getDependents()
    {
        return $this->dependents;
    }

    public function getId()
    {
        return $this->reference->getId();
    }

    /**
     * @return Reference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return Reference
     */
    public function getReferencedObject()
    {
        return $this->reference->getObject();
    }

    public function setStarted($started = true)
    {
        $this->started = $started;
    }

    public function isStarted()
    {
        return $this->started;
    }
}
