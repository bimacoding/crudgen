<?php

return [
        'path' => [

            'migration'         => database_path('migrations/'),

            'model'             => app_path('Models/'),

            'datatables'        => app_path('DataTables/'),

            'repository'        => app_path('Repositories/'),

            'stub'              => base_path('stubs/source/'),

            'routes'            => base_path('routes/web.php'),

            'api_routes'        => base_path('routes/api.php'),

            'request'           => app_path('Http/Requests/'),

            'api_request'       => app_path('Http/Requests/API/'),

            'controller'        => app_path('Http/Controllers/'),

            'api_controller'    => app_path('Http/Controllers/API/'),

            'api_resource'      => app_path('Http/Resources/'),

            'repository_test'   => base_path('tests/Repositories/'),

            'api_test'          => base_path('tests/APIs/'),

            'tests'             => base_path('tests/'),

            'views'             => resource_path('views/'),

            'schema_files'      => resource_path('model_schemas/'),

            'seeder'            => database_path('seeders/'),

            'database_seeder'   => database_path('seeders/DatabaseSeeder.php'),

            'factory'           => database_path('factories/'),
        ],

        /*
        |--------------------------------------------------------------------------
        | Namespaces
        |--------------------------------------------------------------------------
        |
        */

        'namespace' => [

            'model'             => 'App\Models',

            'datatables'        => 'App\DataTables',

            'repository'        => 'App\Repositories',

            'controller'        => 'App\Http\Controllers',

            'api_controller'    => 'App\Http\Controllers\API',

            'api_resource'      => 'App\Http\Resources',

            'request'           => 'App\Http\Requests',

            'api_request'       => 'App\Http\Requests\API',

            'seeder'            => 'Database\Seeders',

            'factory'           => 'Database\Factories',

            'repository_test'   => 'Tests\Repositories',

            'api_test'          => 'Tests\APIs',

            'tests'             => 'Tests',
        ]
    ];
