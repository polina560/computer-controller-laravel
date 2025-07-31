<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Enums\BooleanStatus;
use App\Models\Computer;
use Illuminate\Database\Eloquent\Model;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Resources\ModelResource;
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
            Text::make('ComputerName', 'computer_name'),
            Text::make('FullName', 'full_name'),
            Text::make('IpAddress', 'ip_address'),
            Text::make('MacAddress', 'mac_address'),
            Enum::make('Status', 'status')
                ->default(0)
                ->attach(BooleanStatus::class),
            BelongsTo::make('UserId', 'user', resource: UserResource::class),
        ];
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

    public function filters(): iterable
    {
        return [
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
                    ->error(),
                ActionButton::make('')
                    ->icon('arrow-path')
                    ->method('updateStatus', fn(Model $item): array => ['resourceItem' => $item->getKey()])
                    ->primary(),
                //                ActionButton::make('PowerOn')->method('powerOnList')->bulk()->success(),
                //                ActionButton::make('PowerOff')->method('powerOffList')->bulk()->error(),

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

    public function powerOnList(MoonShineRequest $request): void
    {
        $computers = $request->getResource()->getItems();
        foreach ($computers as $computer) {
            $computer->powerOn();
        }
    }

    public function powerOffList(MoonShineRequest $request): void
    {
        $computers = $request->getResource()->getItems();
        foreach ($computers as $computer) {
            $computer->powerOff();
        }
    }

    public function updateStatus(MoonShineRequest $request): void
    {
        $computer = $request->getResource()->getItem();
        $computer->ping(1);
    }
}
