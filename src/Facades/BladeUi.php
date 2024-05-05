<?php

namespace BladeUi\Facades;

class BladeUi extends \Illuminate\Support\Facades\Facade
{
    /**
     * Obtenga el nombre registrado del componente.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'bladeui';
    }
}