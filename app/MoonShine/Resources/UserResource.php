<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use MoonShine\ImportExport\Contracts\HasImportExportContract;
use MoonShine\ImportExport\ExportHandler;
use MoonShine\ImportExport\Traits\ImportExportConcern;
use MoonShine\Laravel\Enums\Action;
use MoonShine\Laravel\Handlers\Handler;
use MoonShine\Laravel\Http\Responses\MoonShineJsonResponse;
use MoonShine\Laravel\MoonShineRequest;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\ToastType;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\Text;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * @extends ModelResource<User>
 */
class UserResource extends ModelResource implements HasImportExportContract
{
    use ImportExportConcern;

    protected string $model = User::class;

    protected string $title = 'Пользователи';

    protected function exportFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Name'),
            Email::make('Email'),
        ];
    }

    protected function export(): ?Handler
    {
        return ExportHandler::make(__('moonshine::ui.export'))
            ->queue()
            ->filename(sprintf('export_%s', date('Ymd-His')))
            ->dir('/exports')
            ->notifyUsers(fn() => [auth()->id()]);
    }

    protected function import(): ?Handler
    {
        return null;
    }

    protected function activeActions(): ListOf
    {
        return new ListOf(Action::class, [Action::VIEW, Action::DELETE]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function sendVerification(MoonShineRequest $request): MoonShineJsonResponse
    {
        $user = $request->getResource()->getItem();
        $cacheKey = 'verification_request_'.$user->id;
        $throttleLimit = 6; // Лимит запросов
        $throttleTime = 60; // Время в секундах

        // Проверяем, был ли запрос отправлен за последние 1 минуту
        $attempts = cache()->get($cacheKey, 0);

        if ($attempts >= $throttleLimit) {
            return MoonShineJsonResponse::make()->toast(
                'Вы превысили лимит запросов. Попробуйте позже.',
                ToastType::ERROR
            );
        }

        if ($user->hasVerifiedEmail()) {
            return MoonShineJsonResponse::make()->toast('Email уже подтвержден', ToastType::ERROR);
        }

        // Увеличиваем количество попыток и сохраняем в кеше
        cache()->put($cacheKey, $attempts + 1, $throttleTime);

        $user->sendEmailVerificationNotification();

        return MoonShineJsonResponse::make()->toast('Письмо подтверждения отправлено', ToastType::SUCCESS);
    }

    /**
     * @throws Throwable
     */
    protected function indexButtons(): ListOf
    {
        return parent::indexButtons()
            ->prepend(
                ActionButton::make('Отправить письмо подтверждения')
                    ->method('sendVerification', fn(Model $item): array => ['resourceItem' => $item->getKey()])
                    ->async()
                    ->icon('envelope')
            );
    }

    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Имя', 'name'),
            Email::make('Email', 'email'),
            Password::make('Password', 'password'),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            Box::make([
                ...$this->indexFields(),
            ]),
        ];
    }

    protected function detailFields(): iterable
    {
        return [
            ...$this->indexFields(),
        ];
    }

    public function filters(): iterable
    {
        return [
            Text::make('Имя', 'name'),
            Email::make('Email', 'email'),
        ];
    }

    protected function rules(mixed $item): array
    {
        return [];
    }
}
