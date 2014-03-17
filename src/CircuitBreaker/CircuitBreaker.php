<?php

namespace CircuitBreaker;

use CircuitBreaker\Policy\PolicyInterface;
use Closure;
use Exception;
use InvalidArgumentException;

class CircuitBreaker
{
    /**
     * The subject that the CircuitBreaker is wrapping
     *
     * @var object|Closure
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
     * @param object|Closure $subject
     * @param PolicyInterface $policy
     *
     * @return void
     */
    public function __construct($subject, PolicyInterface $policy)
    {
        $this->subject = $subject;
        $this->policy = $policy;
    }

    /**
     * PHP magic __invoke method implementation
     *
     * @throws OpenException
     * @return mixed
     */
    public function __invoke()
    {
        return $this->doRequest($this->subject, func_get_args());
    }

    /**
     * PHP magic __call method implementation
     *
     * @param string $method
     * @param array $parameters
     *
     * @throws InvalidArgumentException
     * @throws OpenException
     * @return mixed
     */
    public function __call($method, $parameters = array())
    {
        if (! is_callable(array($this->subject, $method), false, $callable_name)) {
            throw new InvalidArgumentException('The method ' . $callable_name . ' is not callable');
        }
        return $this->doRequest(array($this->subject, $method), $parameters);
    }

    /**
     * Make the request to the wrapped subject
     *
     * @param Closure $callback   Closure
     * @param array    $parameters Array of parameters
     *
     * @return \CircuitBreakerException\OpenException|\Exception
     * @throws \CircuitBreaker\Exception\OpenException
     */
    protected function doRequest(Closure $callback, array $parameters = array())
    {
        if (! $this->getPolicy()->request($callback, $parameters)) {
            return new \CircuitBreaker\Exception\OpenException();
        }

        try {
            $response = call_user_func_array($callback, $parameters);
        } catch (Exception $exception) {
            $response = $exception;
        }

        if (! $this->getPolicy()->response($response)) {
            throw new \CircuitBreaker\Exception\OpenException();
        }

        if ($response instanceof Exception) {
            throw $response;
        }

        return $response;
    }
}
