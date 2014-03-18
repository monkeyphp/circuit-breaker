<?php
require_once '../vendor/autoload.php';

use CircuitBreaker\CircuitBreaker;
use CircuitBreaker\Policy\PolicyInterface;

/**
 * In this example - we'll create a passive optimistic Policy that always
 * returns true
 */
class OptimisticPolicy implements PolicyInterface
{
    public function request($callback, $parameters = array())
    {
        echo 'Policy is inspecting the Request' . PHP_EOL;
        return true;
    }

    public function response($response)
    {
        echo 'Policy is inspecting the Response' . PHP_EOL;
        return true;
    }
}

/**
 * Create a subject
 *
 * This the functionality that we wish to wrap with a CircuitBreaker.
 * In this example - we'll use a function as our subject.
 */
$subject = function ($count) {
    echo 'Subject has been called with ' . $count . PHP_EOL;
};

/**
 * Create a Policy
 */
$policy = new OptimisticPolicy();

/**
 * Create the CircuitBreaker
 */
$circuitBreaker = new CircuitBreaker($subject, $policy);

/**
 * We'll call our CircuitBreaker several times
 */
for ($i = 0; $i < 5; $i++) {
    echo '-----------------------' . PHP_EOL;
    echo 'Call #' . $i . PHP_EOL;
    $circuitBreaker($i);
}
