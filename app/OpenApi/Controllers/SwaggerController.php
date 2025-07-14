<?php

namespace App\OpenApi\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes\Info;
use OpenApi\Attributes\SecurityScheme;
use OpenApi\Attributes\Server;
use OpenApi\Generator;
use OpenApi\Util;

#[Info(version: '1.0', title: 'Application API')]
#[Server(url: '', description: 'current')]
#[SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    name: 'bearerAuth',
    in: 'header',
    bearerFormat: 'JWT',
    scheme: 'bearer'
)]
class SwaggerController extends Controller
{
    public function json(): JsonResponse
    {
        // Папки, где искать атрибуты
        $dirs = [
            app_path('OpenApi/Controllers'),
            app_path('OpenApi/Attributes'),
            app_path('Http/Controllers'),
            app_path('Models'),
        ];

        // Генерируем OpenAPI-объект
        $openapi = Generator::scan(Util::finder($dirs));

        return response()->json($openapi);
    }
}
