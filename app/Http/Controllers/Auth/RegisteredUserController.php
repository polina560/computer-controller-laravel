<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\OpenApi\Attributes\RequestFormData;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Response;

#[Post(
    path: '/api/register',
    operationId: 'registerUser',
    description: 'Регистрация нового пользователя',
    summary: 'Регистрация',
    tags: ['Auth']
)]
#[RequestFormData(
    requiredProps: ['name', 'email', 'password', 'password_confirmation'],
    properties: [
        new Property(property: 'name', description: 'Имя пользователя', type: 'string'),
        new Property(property: 'email', description: 'Email', type: 'string'),
        new Property(property: 'password', description: 'Пароль', type: 'string'),
        new Property(property: 'password_confirmation', description: 'Подтверждение пароля', type: 'string'),
    ]
)]
#[Response(
    response: 201,
    description: 'Успешная регистрация',
    content: new JsonContent(
        properties: [
            new Property(property: 'message', type: 'string', example: 'User Created'),
            new Property(property: 'user', properties: [
                new Property(property: 'id', description: 'ID пользователя', type: 'integer', example: 1),
                new Property(property: 'name', description: 'Имя пользователя', type: 'string', example: 'John Doe'),
                new Property(
                    property: 'email',
                    description: 'Email пользователя',
                    type: 'string',
                    example: 'john@example.com'
                ),
                new Property(
                    property: 'created_at',
                    description: 'Дата создания пользователя',
                    type: 'string',
                    format: 'date-time',
                    example: '2025-04-16T12:00:00Z'
                ),
                new Property(
                    property: 'updated_at',
                    description: 'Дата последнего обновления пользователя',
                    type: 'string',
                    format: 'date-time',
                    example: '2025-04-16T12:00:00Z'
                ),
            ], type: 'object'),
        ]
    )
)]
#[Response(
    response: 422,
    description: 'Ошибка валидации',
    content: new JsonContent(
        properties: [
            new Property(
                property: 'message', type: 'string', example: 'Такое значение поля email адрес уже существует.'
            ),
            new Property(
                property: 'errors',
                properties: [
                    new Property(
                        property: 'email',
                        type: 'array',
                        items: new Items(
                            type: 'string',
                            example: 'Значение поля email адрес должно быть действительным электронным адресом.'
                        )
                    ),
                    new Property(
                        property: 'password',
                        type: 'array',
                        items: new Items(
                            type: 'string',
                            example: 'Количество символов в поле пароль должно быть не меньше 8.'
                        )
                    ),
                ],
                type: 'object'
            ),
        ]
    )
)]
class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
        ]);

        event(new Registered($user));

        // Auth::login($user);

        // return response()->noContent();
        return response()->json(['message' => 'User Created', 'user' => $user], 201);
    }
}
