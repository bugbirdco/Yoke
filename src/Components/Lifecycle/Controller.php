<?php

namespace BugbirdCo\Yoke\Components\Lifecycle;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class Controller
{
    /**
     * @param string $event
     * @return JsonResponse
     */
    protected function emitter($event)
    {
        /** @var Event $event */
        try {
            $event = new $event(Payload::jsonMake(request()->getContent()));
        } catch (ValidationException $e) {
            return response()->json(null, 400);
        } catch (\Throwable $e) {
            if(request()->wantsJson()) {
                return response()->json(null, 500);
            }
            throw $e;
        }
        event($event);
        return $event->toResponse();
    }

    /**
     * @return JsonResponse
     */
    public function installedEmitter()
    {
        return $this->emitter(InstalledEvent::class);
    }

    /**
     * @return JsonResponse
     */
    public function enabledEmitter()
    {
        return $this->emitter(EnabledEvent::class);
    }

    /**
     * @return JsonResponse
     */
    public function disabledEmitter()
    {
        return $this->emitter(DisabledEvent::class);
    }

    /**
     * @return JsonResponse
     */
    public function uninstalledEmitter()
    {
        return $this->emitter(UninstalledEvent::class);
    }
}
