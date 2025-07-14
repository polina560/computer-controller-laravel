<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\Text as TextModel;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;

/**
 * @extends ModelResource<TextModel>
 */
class TextResource extends ModelResource
{
    protected string $model = TextModel::class;

    protected string $title = 'Тексты';

    /**
     * @return list<FieldContract>
     */
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Ключ', 'key'),
            Text::make('Значение', 'value'),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function formFields(): iterable
    {
        return [
            Box::make([
                ...$this->indexFields(),
            ]),
        ];
    }

    /**
     * @return list<FieldContract>
     */
    protected function detailFields(): iterable
    {
        return [
            ...$this->indexFields(),
        ];
    }

    /**
     * @param  Text  $item
     *
     * @return array<string, string[]|string>
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'key' => ['required', 'string'],
            'value' => ['required', 'string', 'min:2'],
        ];
    }
}
