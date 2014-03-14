<?php
namespace OhUpload;

class Autoloader
{
    public function __autoload()
    {
        spl_autoload_register(
            function($class)
            {
                $dir = realpath(__DIR__ . '/../');
                $path = implode(
                    '',
                    array(
                        $dir,
                        DIRECTORY_SEPARATOR,
                        implode(DIRECTORY_SEPARATOR, explode('\\', $class)),
                        '.php'
                    )
                );
                if (file_exists($path)) {
                    require_once $path;
                    return;
                }
            }
        );
    }
}
