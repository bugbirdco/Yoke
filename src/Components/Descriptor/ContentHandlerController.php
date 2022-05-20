<?php

namespace BugbirdCo\Yoke\Components\Descriptor;

use BugbirdCo\Yoke\Facades\Descriptor;
use BugbirdCo\Yoke\Models\Descriptor\Module\Module;
use Closure;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentHandlerController extends Controller
{
    public function __invoke($content)
    {
        /** @var ?Module $module */
        $module = collect(Descriptor::modules())
            ->filter(function (string $module) use ($content) {
                /** @var class-string<Module>|Module $module */
                return $module::getKey() == $content;
            })
            ->first();

        if (!is_null($module)) {
            $content = $module::content();
            if ($content instanceof Closure) return app()->call($content);
            if (is_object($content)) return $content;
        }

        throw new NotFoundHttpException();
    }
}