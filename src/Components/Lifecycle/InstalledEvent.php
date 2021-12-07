<?php

namespace BugbirdCo\Yoke\Components\Lifecycle;

class InstalledEvent extends Event
{
    public static function rules()
    {
        return [
            'key' => 'required|string',
            'clientKey' => 'required|string',
            'publicKey' => 'string',
            'accountId' => 'string',
            'sharedSecret' => 'required|string',
            'serverVersion' => 'required|string',
            'pluginsVersion' => 'required|string',
            'baseUrl' => 'required|string',
            'displayUrl' => 'string',
            'displayUrlServicedeskHelpCenter' => 'string',
            'productType' => 'required|string',
            'description' => 'required|string',
            'serviceEntitlementNumber' => 'string',
            'oauthClientId' => 'string',
            'eventType' => 'required|string',
        ];
    }
}
