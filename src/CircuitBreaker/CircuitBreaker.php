<?php

namespace CircuitBreaker;

use CircuitBreaker\Exception\OpenException;
use CircuitBreaker\Policy\PolicyInterface;
use Closure;
use Exception;
use InvalidArgumentException;

class CircuitBreaker
{
    /**
     * The subject that the CircuitBreaker is wrapping
     *
     * @var object|array|Closure
     */
    protected $subject;

    /**
     * PolicyInterface that will handle process incoming requests and
     * inspect responses from the wrapped subject
     *
     * @var PolicyInterface
     */
    protected $policy;

    /**
     * Constructor
     *
     * @param object|Closure|array $subject The subject
     * @param PolicyInterface      $policy  The Policy
     *
     * @return void
     */
    public function __construct($subject, PolicyInterface $policy)
    {
        $this->setSubject($subject);
        $this->setPolicy($policy);
    }

    /**
     * PHP magic __invoke method implementation
     *
     * @throws OpenException
     * @return mixed
     */
    public function __invoke()
    {
        return $this->doRequest($this->getSubject(), func_get_args());
    }

    /**
     * PHP magic __call method implementation
     *
     * If the method that is passed to the __call function is not a method that we
     * are interested in wrapping - we simply proxy the call to the service without
     * intervention.
     * 
     * @param string $method
     * @param array  $parameters
     *
     * @throws InvalidArgumentException
     * @throws OpenException
     * @return mixed
     */
    public function __call($method, $parameters = array())
    {
        $callable = (is_array($this->getSubject())) ? $this->getSubject() : array($this->getSubject(), $method);

        if (! is_callable($callable, false, $callable_name)) {
            throw new InvalidArgumentException('The method ' . $callable_name . ' is not callable');
        }

        if ($method != $callable[1]) {
            return call_user_func_array(array($callable[0], $method), $parameters);
        }

        return $this->doRequest($callable, $parameters);
    }

    /**
     * Return the subject that this CircuitBreaker wraps
     *
     * @return callable|array|object
     */
    protected function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the subject that this CircuitBreaker is wrapping
     *
     * @param callable|array|object $subject
     *
     * @return CircuitBreaker
     */
    protected function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Return the Policy instance that will manage this CircuitBreaker
     *
     * @return PolicyInterface
     */
    protected function getPolicy()
    {
        return $this->policy;
    }

    /**
     * Set the Policy instance
     *
     * @param PolicyInterface $policy
     *
     * @return CircuitBreaker
     */
    protected function setPolicy(PolicyInterface $policy)
    {
        $this->policy = $policy;
        return $this;
    }

    /**
     * Make the request to the wrapped subject
     *
     * @param array|Closure $callback   Closure
     * @param array    $parameters Array of parameters
     *
     * @return mixed
     * @throws OpenException|Exception
     */
    protected function doRequest($callback, array $parameters = array())
    {
        if (! $this->getPolicy()->request($callback, $parameters)) {
            throw new OpenException();
        }

        try {
            $response = call_user_func_array($callback, $parameters);
        } catch (Exception $exception) {
            $response = $exception;
        }

        if (! $this->getPolicy()->response($response)) {
            throw new OpenException();
        }

        if ($response instanceof Exception) {
            throw $response;
        }

        return $response;
    }
}
