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
 * @property string|null $accountId
 * @property string $sharedSecret
 * @property string $baseUrl
 * @property string $displayUrl
 * @property string $displayUrlServicedeskHelpCenter
 * @property string $productType
 * @property string $description
 * @property string|null $serviceEntitlementNumber
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
        if (empty($data->source()['displayUrl']))
            $data = new Data([
                    'displayUrl' => $data->source()['baseUrl']
                ] + $data->source());
        if (empty($data->source()['displayUrlServicedeskHelpCenter']))
            $data = new Data([
                    'displayUrlServicedeskHelpCenter' => $data->source()['baseUrl']
                ] + $data->source());

        parent::__construct($data);
    }
}
