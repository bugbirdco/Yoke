<?php

namespace BugbirdCo\Yoke\Components\Framework;

use BugbirdCo\Yoke\Components\Lifecycle\InstalledEvent;
use BugbirdCo\Yoke\Components\Lifecycle\Payload;
use BugbirdCo\Yoke\Models\Auth\Tenant;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
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
                $header = json_decode(base64_decode(explode('.', $jwt)[0]), true);

                // Check we get a GUID in the JWT header
                if (preg_match('/^[a-z\d]{8}-([a-z\d]{4}-){3}[a-z\d]{12}$/', $header['kid'])) {
                    // If we don't fail, update the Tenant's record
                    JWT::decode(
                        $jwt,
                        new Key(
                            file_get_contents(Payload::INSTALL_KEYS_URL . $header['kid']),
                            'RS256'
                        )
                    );
                    $existing->update($data->toArray());
                    return;
                }
            } catch (\Exception $e) {
                report($e);
            }

            abort(401);
        } else {
            Tenant::query()->create($data->toArray());
        }
    }
}