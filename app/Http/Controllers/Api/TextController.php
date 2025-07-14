<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TextResource;
use App\Models\Text;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Response;

class TextController extends Controller
{
    /**
     * Returns a list of Text's
     */
    #[Get(
        path: '/api/texts',
        operationId: 'text-index',
        description: 'Возвращает полный список текстов',
        summary: 'Список текстов',
        security: [['bearerAuth' => []]],
        tags: ['Text']
    )]
    #[Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(properties: [
            new Property(property: 'data', type: 'array', items: new Items(ref: '#/components/schemas/Text'))
        ])
    )]
    public function index(): AnonymousResourceCollection
    {
        return TextResource::collection(Text::all());
    }
}
