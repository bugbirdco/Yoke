<?php

namespace BugbirdCo\Yoke\Models\Descriptor\Action;

use ArrayAccess;
use BugbirdCo\Yoke\Components\Client\Client;
use BugbirdCo\Yoke\Models\Auth\Tenant;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\MessageInterface;

/**
 * Class Action
 * @package BugbirdCo\Yoke\Models\Descriptor\Action
 */
abstract class Action implements ArrayAccess
{
    public abstract static function scopes(): array;

    /**
     * @param Client $client
     * @param array $args
     * @return MessageInterface|PromiseInterface|PromiseInterface[]
     */
    public abstract static function request(Client $client, $args = []);

    public static function parse(MessageInterface $response, Action $dehydrated)
    {
        $dehydrated->setData(json_decode($response->getBody()->getContents(), true));
    }

    public static function make(...$args): Action
    {
        return new static(...$args);
    }

    private $data;
    private $args;

    /** @var Tenant|null */
    protected $tenant;

    public function __construct(...$args)
    {
        $this->args = $args;
    }

    public function getArgs()
    {
        return $this->args;
    }

    public function setTenant(Tenant $tenant)
    {
        $this->tenant = $tenant;
        return $this;
    }

    public function getTenant()
    {
        return $this->tenant;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return (array)$this->data;
    }

    /**
     * @param MessageInterface|PromiseInterface|PromiseInterface[] $messages
     * @return $this
     */
    public function hydrate($messages)
    {
        if (is_array($messages)) {
            foreach ($messages as $message) {
                $this->hydrate($message);
            }
        } else {
            if ($messages instanceof PromiseInterface) $messages = $messages->wait();
            static::parse($messages, $this);
        }

        return $this;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function offsetExists($offset)
    {
        return data_get($this->data, $offset, null) !== null;
    }

    public function offsetGet($offset)
    {
        return data_get($this->data, $offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new \Exception('Action results are immutable'); // TODO: Custom exception
    }

    public function offsetUnset($offset)
    {
        throw new \Exception('Action results are immutable');
    }
}
