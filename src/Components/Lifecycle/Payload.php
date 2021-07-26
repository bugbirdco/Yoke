<?php

namespace BugbirdCo\Yoke\Components\Lifecycle;

use BugbirdCo\Cabinet\Data\Data;
use BugbirdCo\Cabinet\Data\JsonData;
use BugbirdCo\Cabinet\Model;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class LifecyclePayload
 * @package BugbirdCo\Yoke\Models
 *
 * @property string $key
 * @property string $clientKey
 * @property string|null $publicKey
 * @property string|null $accountId
 * @property string $sharedSecret
 * @property string $serverVersion
 * @property string $pluginsVersion
 * @property string $baseUrl
 * @property string $displayUrl
 * @property string $displayUrlServicedeskHelpCenter
 * @property string $productType
 * @property string $description
 * @property string|null $serviceEntitlementNumber
 * @property string|null $oauthClientId
 * @property string $eventType
 */
class Payload extends Model
{
    /**
     * Payload constructor.
     * @param Data $data
     * @throws \Illuminate\Validation\ValidationException
     * @throws \ReflectionException
     */
    public function __construct(Data $data)
    {
        /** @var Validator $validator */
        $validator = validator($data->source(), [
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
        ]);

        if ($validator->fails()) {
            report('Validator failed with: ' . json_encode($validator->errors()));
            $validator->validate();
        }

        if (empty($data->source()['displayUrl']))
            $data = new Data([
                    'displayUrl' => $data->baseUrl
                ] + $data->source());
        if (empty($data->source()['displayUrlServicedeskHelpCenter']))
            $data = new Data([
                    'displayUrlServicedeskHelpCenter' => $data->baseUrl
                ] + $data->source());

        parent::__construct($data);
    }

    /**
     * @param $rawJson
     * @return static
     * @throws \Illuminate\Validation\ValidationException
     * @throws \ReflectionException
     */
    public static function jsonMake($rawJson)
    {
        return new static(new JsonData($rawJson));
    }
}
