<?php

declare(strict_types=1);

namespace App\MoonShine\Layouts;

use App\MoonShine\Pages\LogPage;
use App\MoonShine\Resources\ComputerResource;
use App\MoonShine\Resources\UserResource;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\MenuManager\MenuItem;
use MoonShine\UI\Components\Layout\Layout;
use Override;

final class MoonShineLayout extends AppLayout
{
    #[Override]
    protected function assets(): array
    {
        return [
            ...parent::assets(),
        ];
    }

    #[Override]
    protected function menu(): array
    {
        return [
            ...parent::menu(),
            MenuItem::make('Пользователи', UserResource::class),
            //            MenuItem::make('Тексты', TextResource::class),
            MenuItem::make('Компьютеры', ComputerResource::class),
            MenuItem::make('Логи', LogPage::class),
        ];
    }

    /**
     * @param  ColorManager  $colorManager
     */
    #[Override]
    protected function colors(ColorManagerContract $colorManager): void
    {
        parent::colors($colorManager);
        // $colorManager->primary('#00000');
    }

    #[Override]
    protected function getFooterMenu(): array
    {
        return [];
    }

    #[Override]
    protected function getFooterCopyright(): string
    {
        return '';
    }

    #[Override]
    public function build(): Layout
    {
        return parent::build();
    }
}
