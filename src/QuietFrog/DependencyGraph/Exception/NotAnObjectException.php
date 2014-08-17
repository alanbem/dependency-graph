<?php

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */

namespace QuietFrog\DependencyGraph\Exception;

/**
 * NotAnObjectException class
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class NotAnObjectException extends \InvalidArgumentException
{
    /**
     * @var mixed
     */
    private $parameter;

    /**
     * Constructor.
     *
     * @param mixed $parameter
     */
    public function __construct($parameter)
    {
        $this->parameter = $parameter;

        $message = sprintf('Expected object, parameter of type "%s" given.', gettype($parameter));

        parent::__construct($message);
    }

    /**
     * @return mixed
     */
    public function getParameter()
    {
        return $this->parameter;
    }
} 
