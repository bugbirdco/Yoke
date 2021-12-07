<?php

namespace BugbirdCo\Yoke\Components\Framework;

use BugbirdCo\Yoke\Components\Lifecycle\InstalledEvent;
use BugbirdCo\Yoke\Models\Auth\Tenant;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;

class CreateTenant
{
    public function handle(InstalledEvent $event)
    {
        $data = collect($event->getPayload()->jsonSerialize())->mapWithKeys(function ($item, $key) {
            return [Str::snake($key) => $item];
        })->only(Tenant::fillables())->filter();

        /** @var Tenant $existing */
        if ($existing = Tenant::query()->find($event->getPayload()->clientKey)) {
            $jwt = str_replace('JWT ', '', request()->header('authorization'));
            try {
                JWT::decode($jwt, $existing->shared_secret, ['HS256']);
                $existing->update($data->toArray());
            } catch (\Exception $e) {
                abort(401);
            }
        } else {
            Tenant::query()->create($data->toArray());
        }
    }
}