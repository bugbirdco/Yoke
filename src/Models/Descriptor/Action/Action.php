<?php

namespace BugbirdCo\Yoke\Models\Descriptor\Action;

use ArrayAccess;
use BugbirdCo\Yoke\Components\Client\Client;
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
     * @return MessageInterface
     */
    public abstract static function request(Client $client, $args = []): MessageInterface;

    private $data;

    public function __construct(MessageInterface $message)
    {
        $this->data = json_decode($message->getBody()->getContents());
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
        throw new \Exception('Action result are immutable');
    }

    public function offsetUnset($offset)
    {
        throw new \Exception('Action result are immutable');
    }
}
