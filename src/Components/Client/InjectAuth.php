<?php

namespace BugbirdCo\Yoke\Components\Client;

use BugbirdCo\InTime\InTime;
use BugbirdCo\Yoke\Models\Auth\Tenant;
use Firebase\JWT\JWT;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

/**
 * Class InjectAuth
 * @package BugbirdCo\Yoke\Components\Client
 *
 * Inspired by brezzhnev/atlassian-connect-core
 * @see https://github.com/brezzhnev/atlassian-connect-core/blob/master/src/Http/Auth/QSH.php
 */
class InjectAuth
{
    public static $tokenDuration = 'PT30S';

    /** @var Client */
    private $client;

    protected static $prefixes = [
        '/wiki'
    ];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function __invoke(callable $next)
    {
        return function (RequestInterface $request, array $options) use ($next) {
            $token = JWT::encode($this->payload($request), $this->client->getTenant()->shared_secret);

            $request = new Request(
                $request->getMethod(),
                $request->getUri(),
                array_merge($request->getHeaders(), ['Authorization' => "JWT {$token}"]),
                $request->getBody()
            );

            return $next($request, $options);
        };
    }

    protected function payload(RequestInterface $request)
    {
        return [
            'iss' => $this->client->getTenant()->key,
            'iat' => $time = time(),
            'exp' => $time + InTime::fromExpression(static::$tokenDuration)->inSeconds(),
            'qsh' => $this->hash($request->getMethod(), $request->getUri()),
        ];
    }

    /**
     * Create a QSH string.
     *
     * More details:
     * https://docs.atlassian.com/DAC/bitbucket/concepts/qsh.html
     *
     * @return string
     */
    public function hash(string $method, string $uri): string
    {
        $url = parse_url($uri);

        $parts = [
            $method,
            $this->canonicalUri(data_get($url, 'path', '/')),
            $this->canonicalQuery(data_get($url, 'query', ''))
        ];

        return hash('sha256', implode('&', $parts));
    }

    /**
     * Make a canonical URI.
     *
     * @return string|null
     */
    public function canonicalUri(string $url)
    {
        // Remove a prefix of instance from the path
        // Eg. remove `/wiki` part which means Confluence instance.
        $prefixes = '(?:' . implode(')|(:?', static::$prefixes) . ')';
        $pattern = '/^' . preg_quote($prefixes, '/') . '/';
        $url = preg_replace($pattern, '', $url);

        // The canonical URI should not contain & characters.
        // Therefore, any & characters should be URL-encoded to %26.
        $url = str_replace('&', '%26', $url);

        // The canonical URI only ends with a / character if it is the only character.
        $url = preg_replace('/^\/{2,}$/', '/', $url);

        return $url;
    }

    /**
     * Make a canonical query string.
     *
     * @return string|null
     */
    public function canonicalQuery(string $query)
    {
        parse_str(ltrim($query, '?'), $params);
        ksort($params);

        $query = '';
        foreach ($params as $key => $value) {
            if (in_array($key, ['jwt']))
                continue;

            $query .= '&' . rawurlencode(rawurldecode(str_replace('+', ' ', $key)));

            if (!empty($value))
                $query .= '=' . rawurlencode(rawurldecode(str_replace('+', ' ', $value)));
        }

        // Encode underscores
        $query = substr($query, 1);
        $query = str_replace('_', '%20', $query);

        return $query;
    }
}
