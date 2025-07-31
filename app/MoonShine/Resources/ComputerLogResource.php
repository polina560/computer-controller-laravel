<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\ComputerLog;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\Number;

/**
 * @extends ModelResource<ComputerLog>
 */
class ComputerLogResource extends ModelResource
{
    protected string $model = ComputerLog::class;

	protected array $with = ['computer'];

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
			BelongsTo::make('ComputerId', 'computer', resource: ComputerResource::class),
			Number::make('Status', 'status'),
        ];
    }

    public function formFields(): iterable
    {
        return [
            Box::make([
                ...$this->indexFields()
            ])
        ];
    }

    public function detailFields(): iterable
    {
        return [
            ...$this->indexFields()
        ];
    }

    public function filters(): iterable
    {
        return [
        ];
    }

    public function rules(mixed $item): array
    {
        // TODO change it to your own rules
        return [
			'computer_id' => ['int', 'nullable'],
			'status' => ['int', 'nullable'],
        ];
    }
}
