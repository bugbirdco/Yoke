<?php

namespace BugbirdCo\Yoke\Components\Lifecycle;

use BugbirdCo\Cabinet\Data\Data;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;

abstract class Event
{
    protected $payload;
    protected $response;

    /**
     * @param Data $data
     * @return Data
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function validate(Data $data)
    {
        /** @var Validator $validator */
        $validator = validator($data->source(), static::rules());

        if ($validator->fails()) {
            report('Validator failed with: ' . json_encode($validator->errors()));
            $validator->validate();
        }

        return $data;
    }

    abstract public static function rules();

    public function __construct(Payload $payload)
    {
        $this->payload = $payload;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function setResponse(Response $response, bool $overwrite = false)
    {
        if (empty($this->response) || $overwrite)
            $this->response = $response;
    }

    public function toResponse()
    {
        return $this->response ?? response()->json(null, 204);
    }
}
