<?php

namespace App\Providers;

use App\Contracts\AccountHandler;
use App\Services\AccountService;
use App\Services\DateRangeService;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Entry;
use Filament\Notifications\Livewire\Notifications;
use Filament\Support\Assets\Js;
use Filament\Support\Enums\Alignment;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     */
    public array $bindings = [
        AccountHandler::class => AccountService::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(DateRangeService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Column::configureUsing(fn (Column $l) => $l->translateLabel());
        Filter::configureUsing(fn (Filter $l) => $l->translateLabel());
        Entry::configureUsing(fn (Entry $l) => $l->translateLabel());
        Field::configureUsing(fn (Field $l) => $l->translateLabel());
        BaseFilter::configureUsing(fn (BaseFilter $l) => $l->translateLabel());
        Placeholder::configureUsing(fn (Placeholder $l) => $l->translateLabel());

        Notifications::alignment(Alignment::Center);

        $this->configurePanelSwitch();

        FilamentAsset::register([
            Js::make('TopNavigation', __DIR__ . '/../../resources/js/TopNavigation.js'),
        ]);
    }

    /**
     * Configure the panel switch.
     */
    protected function configurePanelSwitch(): void
    {
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch
                ->modalHeading('Switch Panel')
                ->modalWidth('md')
                ->slideOver()
                ->excludes(['admin'])
                ->iconSize(16)
                ->icons(function () {
                    if (auth()->user()?->belongsToCompany(auth()->user()?->currentCompany)) {
                        return [
                            'user' => 'heroicon-o-user',
                            'company' => 'heroicon-o-building-office',
                        ];
                    }

                    return [
                        'user' => 'heroicon-o-user',
                        'company' => 'icon-building-add',
                    ];
                });
        });
    }
}
