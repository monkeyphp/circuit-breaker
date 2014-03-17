<?php

namespace spec\CircuitBreaker;

use PhpSpec\ObjectBehavior;

class CircuitBreakerSpec extends ObjectBehavior
{
    function it_invokes_a_callback()
    {
        $callable = function() { return 'callback response'; };

        $this->beConstructedWith($callable);

        $this()->shouldReturn('foo');
    }

    function it_invokes_object_method()
    {
        $mockService = new MockService();

        $this->beConstructedWith(array($mockService, 'doSomething'));

        $this()->shouldReturn('did something');
    }

    function it_calls_object_method()
    {
        $mockService = new MockService();

        $this->beConstructedWith($mockService);

        $this->doSomething()->shouldReturn('did something');
    }

    function it_throws_exception_if_object_method_does_not_exist()
    {
        $mockService = new MockService();

        $this->beConstructedWith($mockService);

        $this->shouldThrow('\InvalidArgumentException')->during('doNothing');
    }

    function it_calls_object_magic_method()
    {
        $magicService = new MagicService();

        $this->beConstructedWith($magicService);

        $this->magic()->shouldReturn('Magic called');
    }

    function it_calls_object_invoke_method()
    {
        $invokableService = new InvokableService();

        $this->beConstructedWith($invokableService);

        $this()->shouldReturn('Invoked');
    }

    function it_calls_object_invoke_method_with_parameters()
    {
        $invokableService = new InvokableService();

        $this->beConstructedWith($invokableService);

        $param = 5;
        $this($param)->shouldReturn('Invoked ' . $param);
    }
}

class MockService
{
    public function doSomething()
    {
        return 'did something';
    }
}

class MagicService
{
    public function __call($method, $arguments = array())
    {
        return 'Magic called';
    }
}

class InvokableService
{
    public function __invoke($param = null)
    {
        return 'Invoked' . (($param) ? ' ' . $param : '');
    }
}