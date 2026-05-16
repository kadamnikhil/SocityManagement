<?php

/*
 * This file is part of Laravel Hashids.
 *
 * (c) Vincent Klaiber <hello@vinkla.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

$config = [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
     */

    'default'     => 'main',

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
     */

    'connections' => [

        'main'        => [
            'salt'   => '870f334fd4ae0697d9f5f5b25bade4ff',
            'length' => 5,
        ],

        'alternative' => [
            'salt'   => 'f5b063e82bcd43fd2f5c2fab0c7ec17a',
            'length' => 5,
        ],

    ],

];
if (!function_exists('registerHashIdsBindings')) {
/**
 * @param $path
 * @param $namespace
 * @return mixed
 */
    function registerHashIdsBindings($config, $path, $namespace)
    {
        $classes = scandir($path);

        foreach ($classes as $idx => $class) {
            
            if (strpos($class, ".") === 0) {
                continue;
            }
            if (is_dir($path . DIRECTORY_SEPARATOR . $class)) {
                $config = registerHashIdsBindings($config, $path . DIRECTORY_SEPARATOR . $class, $namespace . $class . '\\');
                continue;
            }
            
            // if (!$this->ends_with($class, '.php')) {
            //     continue;
            // }
            $class  = basename($class, '.php');
            $config['connections'][$namespace . $class] = [
                'salt'   => $namespace . $class . md5($namespace . $class),
                'length' => 13,
            ];
        }
        return $config;
    }
}
try {
    $config = registerHashIdsBindings($config, app_path() . DIRECTORY_SEPARATOR . 'Models', 'App\\Models\\');
    return $config;
} catch (\Exception $e) {
    //
}
