<?php

namespace BugbirdCo\Yoke\Components\Framework\Actions;

use BugbirdCo\Yoke\Components\Client\Client;
use BugbirdCo\Yoke\Models\Descriptor\Action\Action;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\MessageInterface;

/**
 * Class GetUser
 * @package BugbirdCo\Yoke\Components\Framework\Actions
 *
 * @method self __construct(string $userId)
 */
class GetUser extends Action
{

    public static function scopes(): array
    {
        return [
            'READ'
        ];
    }

    public static function request(Client $client, $args = []): MessageInterface
    {
        return $client->get('rest/api/2/user?accountId=|0|');
    }
}