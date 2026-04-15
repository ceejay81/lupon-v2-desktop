<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Native\Desktop\Contracts\ProvidesPhpIni;
use Native\Desktop\Facades\Menu;
use Native\Desktop\Facades\MenuBar;
use Native\Desktop\Facades\Window;

class NativeAppServiceProvider extends ServiceProvider implements ProvidesPhpIni
{
    /**
     * Create a new service provider instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        parent::__construct($app);
    }

    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        if (! config('nativephp-internal.running')) {
            return;
        }

        Window::open()
            ->title('Luponv2 - Photography Studio Management')
            ->fullscreen()
            ->minWidth(1024)
            ->minHeight(600);

        MenuBar::create()
            ->icon(public_path('images/bulalogo.png'))
            ->withContextMenu(
                Menu::make(
                    Menu::label('Show Luponv2'),
                    Menu::separator(),
                    Menu::quit()
                )
            );
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
