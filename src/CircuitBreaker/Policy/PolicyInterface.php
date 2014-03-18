<?php

namespace CircuitBreaker\Policy;

interface PolicyInterface
{

    /**
     * Request
     */
    public function request($callback, $parameters = array());

    /**
     * Response
     *
     * This method is called by the CircuitBreaker and supplys the response
     * from the wrapped subject.
     *
     * The Policy is expected to return a boolean value indicating that
     * the response is successful of not.
     *
     * If a truthy value is returned, the response is passed onto the calling code.
     * If a falsey value is returned, the CircuitBreaker is expected to return
     * an exception.
     *
     * @return mixed
     */
    public function response($response);
}
