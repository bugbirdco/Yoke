<?php

namespace BugbirdCo\Yoke\Components\Client;

use BugbirdCo\Yoke\Models\Auth\Tenant;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

/**
 * Class ApplyPlaceholders
 * @package BugbirdCo\Yoke\Components\Client
 */
class ResetTenant
{
    private $client;
    private $originalTenant;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->originalTenant = $client->getTenant();
    }

    public function __invoke(callable $next)
    {
        /** @var Tenant $tenant */
        return function (RequestInterface $request, array $options) use ($next) {
            $response = $next($request, $options);

            $this->client->setTenant($this->originalTenant);

            return $response;
        };
    }
}
