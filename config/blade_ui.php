<?php
return [
    /**
     * El valor predeterminado está vacío.
     *    prefix => ''
     *              <x-button />
     *              <x-card />
     *
     * Asegúrese de borrar la vista caché después de cambiar el nombre
     *    php artisan view:clear
     *
     */
    'prefix' => '',
    /**
     * Components settings
     */
    'components' => [
        'spotlight' => [
            'class' => 'App\Support\Spotlight',
        ]
    ]
];

