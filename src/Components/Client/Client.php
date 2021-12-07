<?php

namespace BugbirdCo\Yoke\Components\Client;

use BugbirdCo\Yoke\Models\Auth\Tenant;
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
    /** @var Tenant */
    protected $tenant;

    public function __construct(array $config = [])
    {
        $this->tenant = app('yoke.yield_tenant');

        $stack = HandlerStack::create();

        $stack->before('prepare_body', new ApplyPlaceholders($this), 'apply_placeholders');
        $stack->before('prepare_body', new InjectAuth($this), 'inject_auth');

        $stack->push(new ResetTenant($this), 'reset_tenant');

        parent::__construct([
                'base_uri' => $this->tenant->base_url,
                'handler' => $stack
            ] + $config
        );
    }

    /**
     * @return Tenant
     */
    public function getTenant()
    {
        return $this->tenant;
    }

    public function setTenant(Tenant $tenant)
    {
        $this->tenant = $tenant;
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
