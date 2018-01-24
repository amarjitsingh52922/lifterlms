<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb24aa54546c1e3230a92544cb547cb77
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LLMS\\' => 5,
        ),
        'H' => 
        array (
            'Hashids\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LLMS\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
        'Hashids\\' => 
        array (
            0 => __DIR__ . '/..' . '/hashids/hashids/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb24aa54546c1e3230a92544cb547cb77::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb24aa54546c1e3230a92544cb547cb77::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}