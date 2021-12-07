<?php

namespace BugbirdCo\Yoke\Components\Client;

use BugbirdCo\Yoke\Models\Auth\Tenant;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

/**
 * Class ApplyPlaceholders
 * @package BugbirdCo\Yoke\Components\Client
 *
 * Scan through the request and replace the placeholders. Placeholders are in the format |\d+|
 * or the |t:x| where x is a tenant property
 */
class ApplyPlaceholders
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function __invoke(callable $next)
    {
        return function (RequestInterface $request, array $options) use ($next) {
            $args = $this->client->getPlaceholderArgs() + [
                    't:key' => $this->client->getTenant()->key
                ];

            foreach ($args as $key => $data) {
                $data = (string)$data;
                $request = new Request(
                    $request->getMethod(),
                    str_replace(
                        ["|{$key}|", "%7C{$key}%7C"],
                        $data,
                        $request->getUri()),
                    array_map(function ($header) use ($key, $data) {
                        return array_map(function ($value) use ($key, $data) {
                            return str_replace(["|{$key}|", "%7C{$key}%7C"], $data, $value);
                        }, $header);
                    }, $request->getHeaders()),
                    str_replace(
                        ["|{$key}|", "%7C{$key}%7C"],
                        $data,
                        $request->getBody()),
                    $request->getProtocolVersion()
                );
            }

            return $next($request, $options);
        };
    }
}
