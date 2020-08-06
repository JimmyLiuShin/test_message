<?php

function autoload()
{
    $fileName = [
        dirname(__FILE__) . '/Sql.php',
        dirname(__FILE__) . '/Message.php',
    ];

    foreach ($fileName as $files) {
        if (is_readable($files)) {
            require_once $files;
        }
    }
}

spl_autoload_register('autoload');
