<?php

namespace App\Lib\View;

use App\Lib\Config;

class View
{
    #[test]
    public static function render(string $viewName, $data = [])
    {
        if (!empty($data)) {
            extract($data);
        }
        include Config::get('APP_PATH') . '/Views/' . $viewName . '.php';
    }
}

?>