# Circuit Breaker

PHP implementation of the Circuit Breaker pattern

A CircuitBreaker is an production pattern, described by Robert T Nygard in his
seminal book 'Release It!' [insert amazon link here].

It is a defensive pattern aimed at reducing and eliminating the risk 
of any integration point within your application, such as third party web service;
from affecting the rest of your application.

For example, if your application requires a call to be made to a third party api
that is out of the scope of your control, and that service begins to respond
slowly to requests made to it; this can slow down the responsiveness of your 
application in servicing client requests. This in turn can create a cascading
failure in your application that may lead to complete failure.

The CircuitBreaker works by monitoring the integration point, in the case of this
library by passing requests and responses to a Policy object.

## States

A circuit breaker can be in one of three states:
- Open
- Half-open
- Closed

## Policy Object

The Policy object can determine if the request is allowed to pass through 
to the integration point or not. 
It also is used to inspect the responses from the third party integration and 
may then decide to prevent and further requests to be allowed to pass through 
to the integration point.

The Policy instance must only implement two methods `request` and `response` and 
only needs to return a boolean.

In the case of `request` the returned boolean will determine if the CircuitBreaker
should continue to pass the request on to the integration.

In the case of `response` the returned boolean is used to determine if the
CircuitBreaker should return an `OpenException` to the calling client.








```

$service = new CircuitBreaker(new FlakeyService(), new PolicyProvider());

$response = $myService->doSomething($request);

```    