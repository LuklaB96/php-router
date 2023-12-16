<?php
namespace App\Lib\Routing\Validator;

class RouteValidator
{
    /**
     * Validate route, checks if $_SERVER['REQUEST_URI'] is matching any avaible routes.
     * @param string $route
     * @return bool
     */
    public static function validate(string $route): bool
    {
        if ($route) {
            $params = $_SERVER['REQUEST_URI'];
            $routeParams = explode('/', $params);
            $routeParamsBase = explode('/', $route);

            if (count($routeParamsBase) != count($routeParams)) {
                return false;
            }

            $count = 0;
            foreach ($routeParamsBase as $key => $value) {
                $matching = preg_match("/{.*?}/", $value);
                if ($matching == 0) {
                    if ($routeParams[$count] != $routeParamsBase[$count]) {
                        return false;
                    }
                }
                $count++;
            }
            return true;
        }
        return false;
    }
}

?>