<?php
require_once '../vendor/autoload.php';

use CircuitBreaker\CircuitBreaker;
use CircuitBreaker\Policy\PolicyInterface;

/**
 * Optimistic Policy
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
 * This example shows that we can wrap an subject instance method
 */
class MySubject
{
    public function doSomething($parameter)
    {
        echo 'I have done something with ' . $parameter . PHP_EOL;
    }
}

$subject = new MySubject();

$circuitBreaker = new CircuitBreaker(array($subject, 'doSomething'), new OptimisticPolicy());

for ($i = 0; $i < 5; $i++) {
    $circuitBreaker->doSomething($i);
}
