<?php
/**
 * CircuitBreakerTest.php
 * 
 * @category CircuitBreakerTest
 * @package  CircuitBreakerTest
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
namespace CircuitBreakerTest;

use CircuitBreaker\CircuitBreaker;
use PHPUnit_Framework_TestCase;

/**
 * CircuitBreakerTest
 * 
 * @category CircuitBreakerTest
 * @package  CircuitBreakerTest
 * @author   David White [monkeyphp] <david@monkeyphp.com>
 */
class CircuitBreakerTest extends PHPUnit_Framework_TestCase
{
    public function test_it_is_initialisable()
    {
        $name = 'my-breaker';
        $subject = function() {};
        $circuitBreaker = new CircuitBreaker($name, $subject);
        
        $this->assertInstanceOf('\CircuitBreaker\CircuitBreaker', $circuitBreaker);
    }
    
    public function test_it_is_invokable()
    {
        $name = 'my-breaker';
        $subject = function() {  return 'Spam'; };
        $circuitBreaker = new CircuitBreaker($name, $subject);
        
        $this->assertSame('Spam', $circuitBreaker());
    }
    
    public function test_it_is_invokable_with_params()
    {
        $name = 'my-breaker';
        $subject = function($args) {  return $args; };
        $circuitBreaker = new CircuitBreaker($name, $subject);
        
        $this->assertSame('Eggs', $circuitBreaker('Eggs'));
    }
    
    public function test_it_is_callable()
    {
        $name = 'my-breaker';
        $mock = $this->getMock('stdClass', array('doStuff'));
        $mock->expects($this->once())
            ->method('doStuff')
            ->will($this->returnValue('Eggs'));
        $circuitBreaker = new CircuitBreaker($name, $mock);
        
        $this->assertSame('Eggs', $circuitBreaker->doStuff());
    }
    
    public function test_is_callable_with_params()
    {
        $name = 'my-breaker';
        $mock = $this->getMock('stdClass', array('doStuff'));
        $mock->expects($this->once())
            ->method('doStuff')
            ->with('eggs')
            ->will($this->returnValue('Bad eggs'));
        $circuitBreaker = new CircuitBreaker($name, $mock);
        
        $this->assertSame('Bad eggs', $circuitBreaker->doStuff('eggs'));
    }
}
