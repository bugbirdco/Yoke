<?php

namespace BugbirdCo\Yoke\Components\Client;

use Psr\Http\Message\RequestInterface;

class InjectAuth
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function __invoke(callable $next)
    {
        return function (RequestInterface $request, array $options) use ($next) {
            dump(2);
            return $next($request, $options);
        };
    }
}
