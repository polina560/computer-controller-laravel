<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ComputerResource;
use App\Models\Computer;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Response;

class ComputerController extends Controller
{
    /**
     * Выключение компьютера в локальной сети
     */
    #[Get(
        path: '/api/power-off',
        operationId: 'computer-power-off',
        description: 'Возвращает выключенный компьютер',
        summary: 'Компьютер',
        security: [['bearerAuth' => []]],
        tags: ['Computer']
    )]
    #[Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(properties: [
            new Property(property: 'data', type: 'array', items: new Items(ref: '#/components/schemas/Computer')),
        ])
    )]
    public function actionPowerOff(int $id): AnonymousResourceCollection|string
    {
        $uuid = auth()->check() ? auth()->user()->uuid : null;

        if ($uuid) {
            if ($computer = Computer::findOne(['id' => $id, 'user_id' => $uuid])) {
                $computer->powerOff();

                return ComputerResource::collection(Computer::first());
            }
        }

        return 'нет компьютера';
    }

    /**
     * Включение компьютера в локальной сети
     *
     * @throws Exception
     */
    #[Get(
        path: '/api/power-on',
        operationId: 'computer-power-on',
        description: 'Возвращает включенный компьютер',
        summary: 'Компьютер',
        security: [['bearerAuth' => []]],
        tags: ['Computer']
    )]
    #[Response(
        response: 200,
        description: 'OK',
        content: new JsonContent(properties: [
            new Property(property: 'data', type: 'array', items: new Items(ref: '#/components/schemas/Computer')),
        ])
    )]
    public function actionPowerOn(int $id): AnonymousResourceCollection|string
    {
        $uuid = auth()->check() ? auth()->user()->uuid : null;

        if ($uuid) {
            $computer = Computer::findOne(['id' => $id, 'user_id' => $uuid]);
            $computer->powerOn();

            return ComputerResource::collection(Computer::first());
        }

        return 'нет компьютера';
    }
}
