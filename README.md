# Circuit Breaker

PHP implementation of the Circuit Breaker pattern

```

$service = new CircuitBreaker(new FlakeyService(), new PolicyProvider());

$response = $myService->doSomething($request);

```    


## Run the PHPUnit tests

    $ vendor/bin/phpunit ./tests/