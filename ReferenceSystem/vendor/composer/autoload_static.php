<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit13038dc8b4e269913aca00f3f1d4ee26
{
    public static $prefixLengthsPsr4 = array (
        'Z' => 
        array (
            'Zend\\Escaper\\' => 13,
        ),
        'R' => 
        array (
            'ReferenceSystem\\' => 16,
        ),
        'P' => 
        array (
            'PhpOffice\\PhpWord\\' => 18,
            'PhpOffice\\Common\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Zend\\Escaper\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-escaper/src',
        ),
        'ReferenceSystem\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
        'PhpOffice\\PhpWord\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice/phpword/src/PhpWord',
        ),
        'PhpOffice\\Common\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice/common/src/Common',
        ),
    );

    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'Sunra\\PhpSimple\\HtmlDomParser' => 
            array (
                0 => __DIR__ . '/..' . '/sunra/php-simple-html-dom-parser/Src',
            ),
        ),
    );

    public static $classMap = array (
        'PclZip' => __DIR__ . '/..' . '/pclzip/pclzip/pclzip.lib.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit13038dc8b4e269913aca00f3f1d4ee26::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit13038dc8b4e269913aca00f3f1d4ee26::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit13038dc8b4e269913aca00f3f1d4ee26::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit13038dc8b4e269913aca00f3f1d4ee26::$classMap;

        }, null, ClassLoader::class);
    }
}
