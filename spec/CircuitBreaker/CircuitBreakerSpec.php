<?php

namespace spec\CircuitBreaker;

use CircuitBreaker\Policy\PolicyInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CircuitBreakerSpec extends ObjectBehavior
{
    function it_invokes_a_subject_callback(PolicyInterface $policy)
    {
        $subject = function() { return 'callback response'; };

        $policy->request(Argument::any(), Argument::any())->willReturn(true);
        $policy->response(Argument::any())->willReturn(true);

        $this->beConstructedWith($subject, $policy);

        $this()->shouldReturn('callback response');
    }

    function it_invokes_subject_method(PolicyInterface $policy)
    {
        $subject = new MockService();

        $policy->request(Argument::any(), Argument::any())->willReturn(true);
        $policy->response(Argument::any())->willReturn(true);

        $this->beConstructedWith(array($subject, 'doSomething'), $policy);

        $this()->shouldReturn('did something');
    }

    function it_calls_subject_method(PolicyInterface $policy)
    {
        $subject = new MockService();

        $policy->request(Argument::any(), Argument::any())->willReturn(true);
        $policy->response(Argument::any())->willReturn(true);

        $this->beConstructedWith($subject, $policy);

        $this->doSomething()->shouldReturn('did something');
    }

    function it_throws_exception_if_subject_method_does_not_exist(PolicyInterface $policy)
    {
        $subject = new MockService();

        $policy->request(Argument::any(), Argument::any())->willReturn(true);
        $policy->response(Argument::any())->willReturn(true);

        $this->beConstructedWith($subject, $policy);

        $this->shouldThrow('\InvalidArgumentException')->during('doNothing');
    }

    function it_calls_subject_magic_method(PolicyInterface $policy)
    {
        $subject = new MagicService();

        $policy->request(Argument::any(), Argument::any())->willReturn(true);
        $policy->response(Argument::any())->willReturn(true);

        $this->beConstructedWith($subject, $policy);

        $this->magic()->shouldReturn('Magic called');
    }

    function it_calls_subject_invoke_method(PolicyInterface $policy)
    {
        $subject = new InvokableService();

        $policy->request(Argument::any(), Argument::any())->willReturn(true);
        $policy->response(Argument::any())->willReturn(true);

        $this->beConstructedWith($subject, $policy);

        $this()->shouldReturn('Invoked');
    }

    function it_calls_subject_invoke_method_with_parameters(PolicyInterface $policy)
    {
        $subject = new InvokableService();

        $policy->request(Argument::any(), Argument::any())->willReturn(true);
        $policy->response(Argument::any())->willReturn(true);

        $this->beConstructedWith($subject, $policy);

        $param = 5;
        $this($param)->shouldReturn('Invoked ' . $param);
    }


    function it_returns_open_exception_if_policy_request_returns_false(PolicyInterface $policy)
    {
        $subject = new MockService();
        $policy->request(Argument::any(), Argument::any())->willReturn(false);

        $this->beConstructedWith($subject, $policy);

        $this->shouldThrow('\CircuitBreaker\Exception\OpenException')->during('doSomething');
    }

    function it_returns_open_exception_if_policy_response_returns_false(PolicyInterface $policy)
    {
        $subject = new MockService();

        $policy->request(Argument::any(), Argument::any())->willReturn(true);
        $policy->response(Argument::any())->willReturn(false);

        $this->beConstructedWith($subject, $policy);

        $this->shouldThrow('\CircuitBreaker\Exception\OpenException')->during('doSomething');
    }

    function it_returns_subject_exception_if_policy_response_returns_true(PolicyInterface $policy)
    {
        $subject = new MockService();

        $policy->request(Argument::any(), Argument::any())->willReturn(true);
        $policy->response(Argument::any())->willReturn(true);

        $this->beConstructedWith($subject, $policy);

        $this->shouldThrow('\RuntimeException')->during('doException');
    }
}

class MockService
{
    public function doSomething()
    {
        return 'did something';
    }

    public function doException()
    {
        throw new \RuntimeException();
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