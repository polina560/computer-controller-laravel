<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Enums\BooleanStatus;
use App\Models\Computer;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Http\Responses\MoonShineJsonResponse;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Enums\ToastType;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<Computer>
 */
class ComputerResource extends ModelResource
{
    protected string $model = Computer::class;

    protected array $with = ['user'];

//    protected ?ClickAction $clickAction = ClickAction::EDIT;

    public function getTitle(): string
    {
        return 'Компьютеры';
    }

    public function indexFields(): iterable
    {
        // TODO correct labels values
        return [
            ID::make('id')
                ->sortable(),
            Text::make('ComputerName', 'computer_name')->sortable(),
            Text::make('FullName', 'full_name')->sortable(),
            Text::make('IpAddress', 'ip_address')->sortable(),
            Text::make('MacAddress', 'mac_address')->sortable(),
            Enum::make('Status', 'status')
                ->default(0)
                ->attach(BooleanStatus::class)
                ->sortable(),
            //            Enum::make('Status', 'status')
            //                ->attach(BooleanStatus::class)
            //                ->asyncMethod(
            //                    'changeStatus',
            //                    events: [
            //                        AlpineJs::event(JsEvent::TABLE_UPDATED), // Правильное событие
            //                    ]
            //                )
            //                ->showWhenUpdated(),
            BelongsTo::make('UserId', 'user', 'name', resource: UserResource::class)
                ->afterFill(
                    fn($field) => $field->setColumn('user_id'))
                ->sortable(),
        ];
    }

    public function changeStatus(MoonShineRequest $request): MoonShineJsonResponse
    {
        $item = $this->getItem();
        $status = $request->input('value');

        $item->status = $status;
        $item->save();

        return MoonShineJsonResponse::make()
            ->toast('Status updated')
            ->addEvent(
                AlpineJs::event(JsEvent::TABLE_UPDATED) // Отправляем событие обновления
            );
    }

    public function formFields(): iterable
    {
        return [
            Box::make([
                ...$this->indexFields(),
            ]),
        ];
    }

    public function detailFields(): iterable
    {
        return [
            ...$this->indexFields(),
        ];
    }

    protected function indexButtons(): ListOf
    {
        return parent::indexButtons()
            ->prepend(

                ActionButton::make('')
                    ->icon('power')
                    ->method('powerOn', fn(Model $item): array => ['resourceItem' => $item->getKey()])
                    ->success(),
                ActionButton::make('')
                    ->icon('power')
                    ->method('powerOff', fn(Model $item): array => ['resourceItem' => $item->getKey()])
                    ->error()
                    ->withConfirm(
                        'Подтверждение',
                        'Вы действительно хотите выключить компьютер?',
                        'Да, выключить'
                    ),
                ActionButton::make('')
                    ->icon('arrow-path')
                    ->method('updateStatus', fn(Model $item): array => ['resourceItem' => $item->getKey()])
                    ->primary(),
                ActionButton::make('Включить')->bulk()->icon('power')->method('powerOnList')->success(),
                ActionButton::make('Выключить')->bulk()->icon('power')->method('powerOffList')->error()->withConfirm(
                    'Подтверждение',
                    'Вы действительно хотите выключить выбранные компьютеры?',
                    'Да, выключить'
                ),
                ActionButton::make('Обновить статус')
                    ->bulk()
                    ->icon('arrow-path')
                    ->method('updateStatusList')
                    ->primary()

            );
    }

    public function rules(mixed $item): array
    {
        // TODO change it to your own rules
        return [
            'computer_name' => ['string', 'nullable'],
            'full_name' => ['string', 'nullable'],
            'ip_address' => ['string', 'required'],
            'mac_address' => ['string', 'nullable'],
            'status' => ['int', 'nullable'],
            'user_id' => ['int', 'nullable'],
        ];
    }

    protected function search(): array
    {
        return [
            'id',
            'computer_name',
            'full_name',
            'ip_address',
            'mac_address',
            'status',
            'user.name',
        ];
    }

    public function powerOn(MoonShineRequest $request): void
    {
        $computer = $request->getResource()->getItem();
        $computer->powerOn();
    }

    public function powerOff(MoonShineRequest $request): void
    {
        $computer = $request->getResource()->getItem();
        $computer->powerOff();
    }

    public function updateStatus(MoonShineRequest $request): void
    {
        $computer = $request->getResource()->getItem();
        $computer->ping(1);
    }

    public function powerOnList(MoonShineRequest $request): MoonShineJsonResponse
    {
        $ids = $request->array('ids');

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $computers = Computer::whereIn('id', $ids)->get();
        foreach ($computers as $computer) {
            $computer->powerOn();
        }

        return MoonShineJsonResponse::make()
            ->toast(
                'Выбранные компьютеры выключены',
                ToastType::SUCCESS
            );
    }

    public function powerOffList(MoonShineRequest $request): MoonShineJsonResponse
    {
        $ids = $request->array('ids');

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $computers = Computer::whereIn('id', $ids)->get();
        foreach ($computers as $computer) {
            $computer->powerOff();
        }

        return MoonShineJsonResponse::make()
            ->toast(
                'Выбранные компьютеры выключены',
                ToastType::SUCCESS
            );
    }

    public function updateStatusList(MoonShineRequest $request): MoonShineJsonResponse
    {
        $ids = $request->array('ids');

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $processed = 0;
        $computers = Computer::whereIn('id', $ids)->get();
        foreach ($computers as $computer) {
            $processed++;
            $computer->ping();
        }

        return MoonShineJsonResponse::make()
            ->toast(
                'Статус обновлен для '.$processed.' компьютеров',
                $processed > 0 ? ToastType::SUCCESS : ToastType::ERROR
            );
    }
}
