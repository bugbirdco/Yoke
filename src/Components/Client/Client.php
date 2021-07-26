<?php

namespace BugbirdCo\Yoke\Components\Client;

use GuzzleHttp\Client as BaseClient;
use GuzzleHttp\HandlerStack;


/**
 * Class Client
 * @package BugbirdCo\Yoke\Components\Client
 *
 * Inspired by brezzhnev/atlassian-connect-core
 * @see https://github.com/brezzhnev/atlassian-connect-core/blob/master/src/Http/Clients/JWTClient.php
 */
class Client extends BaseClient
{
    public function __construct(array $config = [])
    {
        $stack = HandlerStack::create();

        $stack->before('prepare_body', new ApplyPlaceholders($this), 'apply_placeholders');
        $stack->before('prepare_body', new InjectAuth($this), 'inject_auth');

        parent::__construct([
                'handler' => $stack
            ] + $config
        );
    }

    protected $placeholderArgs;

    public function setPlaceholderArgs(array $args)
    {
        $this->placeholderArgs = $args;
    }

    public function getPlaceholderArgs()
    {
        return $this->placeholderArgs;
    }

    public function delPlaceholderArgs()
    {
        $this->placeholderArgs = null;
    }
}
