<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\OpenApi\Attributes\RequestFormData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;

class LoginController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    #[Post(
        path: '/api/login',
        operationId: 'loginUser',
        description: 'Аутентификация пользователя',
        summary: 'Логин пользователя',
        tags: ['Auth']
    )]
    #[RequestFormData(
        requiredProps: ['email', 'password'],
        properties: [
            new Property(property: 'email', description: 'Email пользователя', type: 'string'),
            new Property(property: 'password', description: 'Пароль пользователя', type: 'string'),
        ]
    )]
    #[\OpenApi\Attributes\Response(
        response: 200,
        description: 'Успешный логин',
        content: new JsonContent(
            properties: [
                new Property(property: 'token', type: 'string', example: 'example-token-here'),
                new Property(property: 'user', properties: [
                    new Property(property: 'name', type: 'string', example: 'ivan'),
                    new Property(property: 'email', type: 'string', example: 'ivan@bk.ru'),
                ],
                    type: 'object'
                ),
            ]
        )
    )]
    #[\OpenApi\Attributes\Response(
        response: 422,
        description: 'Ошибка валидации',
        content: new JsonContent(
            properties: [
                new Property(
                    property: 'message',
                    type: 'string',
                    example: 'Значение поля email адрес должно быть действительным электронным адресом.'
                ),
                new Property(
                    property: 'errors',
                    properties: [
                        new Property(
                            property: 'email',
                            type: 'array',
                            items: new Items(type: 'string', example: 'Неверное имя пользователя или пароль.')
                        ),
                    ],
                    type: 'object'
                ),
            ]
        )
    )]
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $user = User::where('email', $request->email)->first();

        $data = [
            'token' => $user->createToken('token for '.$user->email)->plainTextToken,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];

        return response()->json($data, 201);

        // $request->session()->regenerate();

        // return response()->noContent();
    }

    /**
     * Destroy an authenticated session.
     */
    #[Post(
        path: '/api/logout',
        operationId: 'logoutUser',
        description: 'Удаляет текущую аутентификацию пользователя.',
        summary: 'Логин пользователя',
        security: [['bearerAuth' => []]],
        tags: ['Auth']
    )]
    #[\OpenApi\Attributes\Response(
        response: 204,
        description: 'Успешный выход',
        content: new JsonContent(
            properties: []
        )
    )]
    #[\OpenApi\Attributes\Response(
        response: 401,
        description: 'Ошибка аутентификации',
        content: new JsonContent(
            properties: [
                new Property(property: 'message', type: 'string', example: 'Не авторизован'),
            ]
        )
    )]
    public function destroy(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        // Auth::guard('web')->logout();

        // $request->session()->invalidate();

        // $request->session()->regenerateToken();

        return response()->noContent();
    }
}
