<?php

namespace App\Support;

use App\Routing\Post;
use Illuminate\Support\Facades\Route;
use ReflectionClass;

class AttributeRouteRegistrar
{
    public static function register(string $controller): void
    {
        $reflection = new ReflectionClass($controller);

        foreach ($reflection->getMethods() as $method) {
            foreach ($method->getAttributes(Post::class) as $attribute) {
                $route = $attribute->newInstance();

                Route::post($route->uri, [$controller, $method->getName()])->name($route->name);
            }
        }
    }
}
