<?php

namespace BladeUi;

use BladeUi\View\Components\Button;
use BladeUi\View\Components\Icon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeUiServiceProvider extends ServiceProvider
{
    public function boot():void
    {

        $this->registerComponents();
        $this->registerBladeDirectives();

//        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }
    public function registerComponents()
    {
        // Simplemente cambie el nombre de <x-icon> proporcionado por BladeUI Icons a <x-svg> para no chocar con el nuestro.
        Blade::component('BladeUI\Icons\Components\Icon', 'svg');
       /*  No importa si los componentes tienen prefijo personalizado o no,
         También registramos el siguiente alias para evitar colisiones de nombres.
         porque se utilizan dentro de algunos componentes de bladeui.  */

        Blade::component('bladeui-button', Button::class);
//        Blade::component('bladeui-card', Card::class);
        Blade::component('bladeui-icon', Icon::class);
//        Blade::component('bladeui-input', Input::class);
//        Blade::component('bladeui-list-item', ListItem::class);
//        Blade::component('bladeui-modal', Modal::class);
//        Blade::component('bladeui-menu', Menu::class);
//        Blade::component('bladeui-menu-item', MenuItem::class);
//        Blade::component('bladeui-header', Header::class);
        $prefix = config('blade_ui.prefix');


    }

    public function registerBladeDirectives():void
    {
        $this->registerScopeDirective();
    }

    public function registerScopeDirective():void
    {

        /**
         * Todos los créditos de esta directiva blade son para Konrad Kalemba.
         * Recién copiado y modificado para mi caso de uso muy específico.
         *
         * https://github.com/konradkalemba/blade-components-scoped-slots
         */
        Blade::directive('scope', function ($expression) {
            // Divida la expresión por comas de "nivel superior" (no entre paréntesis)
            $directiveArguments = preg_split("/,(?![^\(\(]*[\)\)])/", $expression);
            $directiveArguments = array_map('trim', $directiveArguments);

            [$name, $functionArguments] = $directiveArguments;

            // La función de compilación "usa" para inyectar variables externas adicionales
            $uses = Arr::except(array_flip($directiveArguments), [$name, $functionArguments]);
            $uses = array_flip($uses);
            array_push($uses, '$__env');
            $uses = implode(',', $uses);

            /**
             *  Los nombres de las ranuras no pueden contener puntos, por ejemplo: `user.city`.
             *  Entonces convertimos `user.city` a `user___city`
             *
             *  Más tarde, el componente será reemplazado nuevamente.
             */
            $name = str_replace('.', '___', $name);

            return "<?php \$__env->slot({$name}, function({$functionArguments}) use ({$uses}) { ?>";
        });

        Blade::directive('endscope', function () {
            return '<?php }); ?>';
        });
    }

    public function register():void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/blade_ui.php', 'blade_ui');

        // Register the service the package provides.
        $this->app->singleton('bladeui', function ($app) {
            return new BladeUi;
        });
    }

    public function provides()
    {
        return ['bladeui'];
    }
    public function bootForConsole(): void
    {
        $this->publishes([
            __DIR__ . '/../config/blade_ui.php' => config_path('blade_ui.php'),
        ], 'blade-ui-config');
        $this->commands(\BladeUiInstallCommand::class);
    }
}