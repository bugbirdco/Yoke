<?php

namespace BugbirdCo\Yoke\Components\Framework\Actions;

use BugbirdCo\Yoke\Components\Client\Client;
use BugbirdCo\Yoke\Models\Descriptor\Action\Action;
use Psr\Http\Message\MessageInterface;

class GetMyself extends Action
{

    public static function scopes(): array
    {
        return [
            'READ'
        ];
    }

    public static function request(Client $client, $args = []): MessageInterface
    {
        return $client->get('/rest/api/2/myself');
    }
}