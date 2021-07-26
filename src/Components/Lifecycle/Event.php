<?php

namespace BugbirdCo\Yoke\Components\Lifecycle;

use Illuminate\Http\Response;

abstract class Event
{
    protected $payload;
    protected $response;

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
        if(empty($this->response) || $overwrite)
            $this->response = $response;
    }

    public function toResponse()
    {
        return $this->response ?? response()->json(null, 204);
    }
}
