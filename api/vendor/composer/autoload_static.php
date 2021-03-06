<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite7e09d78967516685d2dd82bba5f3d99
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'App\\Controllers\\RentalController' => __DIR__ . '/../..' . '/app/Controllers/RentalController.php',
        'App\\Db\\Config' => __DIR__ . '/../..' . '/app/Db/Config.php',
        'App\\Db\\Db' => __DIR__ . '/../..' . '/app/Db/Db.php',
        'App\\Http\\Request' => __DIR__ . '/../..' . '/app/Http/Request.php',
        'App\\Http\\Response' => __DIR__ . '/../..' . '/app/Http/Response.php',
        'App\\Model\\Model' => __DIR__ . '/../..' . '/app/Model/Model.php',
        'App\\Model\\Rental' => __DIR__ . '/../..' . '/app/Model/Rental.php',
        'App\\Routes\\Route' => __DIR__ . '/../..' . '/app/Routes/Route.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite7e09d78967516685d2dd82bba5f3d99::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite7e09d78967516685d2dd82bba5f3d99::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite7e09d78967516685d2dd82bba5f3d99::$classMap;

        }, null, ClassLoader::class);
    }
}
