<?php
/**
 * CircuitBreaker.php
 * 
 * @category CircuitBreaker
 * @package  CircuitBreaker
 * @author   David White [monkeyphp] <david@monkeyphp.com>
 * 
 * Copyright (C) 2014  David White
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see [http://www.gnu.org/licenses/].
 */
namespace CircuitBreaker;

use Closure;
use Exception;

/**
 * CircuitBreaker
 * 
 * @category CircuitBreaker
 * @package  CircuitBreaker
 * @author   David White [monkeyphp] <david@monkeyphp.com>
 */
class CircuitBreaker
{
    /**
     * The name of the circuit breaker
     * 
     * @var string
     */
    protected $name;
    
    /**
     * Callable
     * 
     * @var mixed
     */
    protected $subject;
    
    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct($name, $subject)
    {
        $this->setName($name);
        $this->setSubject($subject);
    }
    
    /**
     * Return the name of the CircuitBreaker
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Return the subject that this CircuitBreaker wraps
     * 
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }
    
    /**
     * Set the name of the CircuitBreaker
     * 
     * @param string $name The name of the CircuitBreaker
     * 
     * @return CircuitBreaker
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Set the callable subject that this CircuitBreaker wraps
     * 
     * @param mixed $subject
     * 
     * @return CircuitBreaker
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }
    
    /**
     * Implementation of PHP magic __invoke method
     * 
     * @throws Exception
     * @return mixed
     */
    public function __invoke()
    {
        $subject = $this->getSubject();
        
        if ($subject instanceof Closure) {
            if (is_callable($subject, false)) {
                return call_user_func_array($subject, func_get_args());
            }
        }
        
        throw new Exception();
    }
    
    /**
     * Implementation of PHPs magic __call method
     * 
     * @param string $name      The name of the method to call
     * @param mixed  $arguments The parameters to supply
     * 
     * @throws Exception
     * @return mixed
     */
    public function __call($name, $parameters)
    {
        $subject = $this->getSubject();
        
        if (is_object($subject)) {
            if (is_callable(array($subject, $name), false)) {
                return call_user_func_array(array($subject, $name), $parameters);
            }
        }
        
        throw new Exception();
    }
}
