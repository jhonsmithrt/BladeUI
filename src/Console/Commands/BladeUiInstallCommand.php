<?php

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use RuntimeException;
use function Laravel\Prompts\select;
class BladeUiInstallCommand extends Command
{
    protected $signature = 'bladeui:install';

    protected $description = 'Instalacion de BladeUi';

    protected $ds = DIRECTORY_SEPARATOR;

    public function handle()
    {
        $this->info("Instalador de BladeUi");
        $this->info("Creado por: @jhonsmithrt");
        $this->info("Version: 1.0.0");
        $this->info("Basado en el proyecto: https://github.com/robsontenorio/mary");
        $this->info("\n");

        // Install Volt ?
        $shouldInstallVolt = $this->askForVolt();
        //Yarn or Npm ?
        $packageManagerCommand = $this->askForPackageInstaller();

        // Install Livewire/Volt
        $this->installLivewire($shouldInstallVolt);

        // Setup Tailwind
        $this->setupTailwind($packageManagerCommand);

    }

    public function installLivewire(string $shouldInstallVolt)
    {
        $this->info("\nInstalando  Livewire...\n");

        $extra = $shouldInstallVolt == 'Yes'
            ? ' livewire/volt && php artisan volt:install'
            : '';

        Process::run("composer require livewire/livewire $extra", function (string $type, string $output) {
            echo $output;
        })->throw();
    }

    public function setupTailwind(string $packageManagerCommand)
    {
        /**
         * Instalando  Tailwindcss
         */
        $this->info("\nInstalando  TailwindCss...\n");

        Process::run("$packageManagerCommand tailwindcss postcss autoprefixer", function (string $type, string $output) {
            echo $output;
        })->throw();

        /**
         * Setup app.css
         */

        $cssPath = base_path() . "{$this->ds}resources{$this->ds}css{$this->ds}app.css";
        $css = File::get($cssPath);

        if (! str($css)->contains('@tailwind')) {
            $stub = File::get(__DIR__ . "/../../../stubs/app.css");
            File::put($cssPath, str($css)->prepend($stub));
        }

        /**
         * Setup tailwind.config.js
         */

        $tailwindJsPath = base_path() . "{$this->ds}tailwind.config.js";

        if (! File::exists($tailwindJsPath)) {
            $this->copyFile(__DIR__ . "/../../../stubs/tailwind.config.js", "tailwind.config.js");
            $this->copyFile(__DIR__ . "/../../../stubs/postcss.config.js", "postcss.config.js");

            return;
        }

        // Clear view cache
        Artisan::call('view:clear');
        $this->info("\n");
        $this->info("✅  ¡Hecho! Ejecute `yarn dev` o `npm run dev` o `bun run dev`");
        $this->info("Intalacion completa. ¡Gracias por usar BladeUI!");
        $this->info("Creado por: @jhonsmithrt");
        $this->info("Version: 1.0.0");
        $this->info("Basado en el proyecto: https://github.com/robsontenorio/mary");
        $this->info("\n");

    }

    public function askForVolt(): string
    {
        return select(
            'Instalar `livewire/volt` ?',
            ['Si', 'No'],
            hint: 'No importa cuál sea tu elección, siempre instala `livewire/livewire`'
        );
    }

    public function askForPackageInstaller(): string
    {
        $os = PHP_OS;
        $findCommand = stripos($os, 'WIN') === 0 ? 'where' : 'which';

        $yarn = Process::run($findCommand . ' yarn')->output();
        $npm = Process::run($findCommand . ' npm')->output();
        $bun = Process::run($findCommand . ' bun')->output();

        $options = [];

        if (Str::of($yarn)->isNotEmpty()) {
            $options = array_merge($options, ['yarn add -D' => 'yarn']);
        }

        if (Str::of($npm)->isNotEmpty()) {
            $options = array_merge($options, ['npm install --save-dev' => 'npm']);
        }

        if (Str::of($bun)->isNotEmpty()) {
            $options = array_merge($options, ['bun i -D' => 'bun']);
        }

        if (count($options) == 0) {
            $this->error("Necesitas yarn o npm o bun instalado.");

            exit;
        }

        return select(
            label: 'Instalar con ...',
            options: $options
        );
    }
}