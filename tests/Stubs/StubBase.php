<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Stubs;

/**
 * Base class creating stubs with spy behaviour.
 *
 * This is take directly from Subscriptions. It can replaced when the stub is available as an external library available
 *  via composer.
 *
 * @coversNothing
 */
abstract class StubBase
{
    /**
     * Storage of all calls made to stubbed methods.
     *
     * Calls are keyed by method name, and as a list of arrays where method names are keys.
     *  eg; two calls calls to a method foo(int $id, string $name) would be stored as:
     *  ['foo' => [['id' => 1, 'name' => 'Bob'],['id' => 15, 'name' => 'Joe']]]
     *
     * @var mixed[]
     */
    private $calls = [];

    /**
     * List of responses to return when a given method is called.
     *
     * Response are keyed by the name of the original method that's called, returned in a FIFO manner.
     *  If Throwable (exception) is in the list, it will be thrown, instead of returned. eg; A method
     *  called 'createAction' that will return an Action entity on the first call, and an BadName exception
     *  will be stored as follows:
     *  ['createAction' => [Action(), BadNameException]]
     *
     * @var mixed[]
     */
    private $responses = [];

    /**
     * StubBase constructor.
     *
     * @param mixed[] $responses An array of responses, keyed by the method that will return them.
     *  See documentation on self::$calls for structure.
     */
    public function __construct(?array $responses = null)
    {
        if ($responses === null) {
            return;
        }
        foreach ($responses as $method => $methodResponses) {
            $this->responses[$method] = $methodResponses;
        }
    }

    /**
     * Get a list of calls made to a particular method.
     *
     * This method should be called from a method called 'getXyzCalls()' where Xyz is the name
     *  of the original method being implemented from the interface. The following code
     *  snippet can be inserted into this function:
     *      return $this->getCalls(__FUNCTION__);
     *
     * @param string $method The method name of the getter function.
     *  This must be named 'getXyzCalls', where Xyz is the method name being spied on.
     *
     * @return mixed[] A list of all the calls made to the original method.
     */
    protected function getCalls(string $method): array
    {
        if (\preg_match('/^get(.*)Calls$/', $method, $matches) !== 1) {
            throw new \RuntimeException("Get method doesn't match required format of getMethodCalls()");
        }
        $method = \lcfirst($matches[1]);

        if (\array_key_exists($method, $this->calls)) {
            return $this->calls[$method];
        }
        return [];
    }

    /**
     * Return the next item queued for response.
     *
     * This can be called with the following snippet:
     *      return $this->returnOrThrowResponse(__FUNCTION__);
     *
     * @param string $method The name of the original method to return the response for.
     *
     * @return mixed A preprogrammed response.
     */
    protected function returnOrThrowResponse(string $method)
    {
        if (\array_key_exists($method, $this->responses) === false) {
            throw new \RuntimeException("No responses found in stub"); // Replace with StubException
        }
        $response = \array_pop($this->responses[$method]);

        if ($response instanceof \Throwable === true) {
            throw $response;
        }
        return $response;
    }

    /**
     * Save all calls made to this method.
     *
     * This can be called with the following snippet as the first line in the method:
     *      $this->saveCalls(__FUNCTION__, \get_defined_vars());
     *
     * @param string $method The method name to save data against.
     * @param mixed[] $args A key/value array of parameter names and their values.
     *
     * @return void
     */
    protected function saveCalls(string $method, array $args): void
    {
        $this->calls[$method][] = $args;
    }
}
