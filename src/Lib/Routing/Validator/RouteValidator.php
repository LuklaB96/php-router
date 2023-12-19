<?php
namespace App\Lib\Routing\Validator;

class RouteValidator
{
    /**
     * Validate route, checks if $_SERVER['REQUEST_URI'] parameters are matching specified route parameters
     *
     * @param  string $route
     * @return bool
     */
    public static function validate(string $route): bool
    {
        if ($route) {
            $params = $_SERVER['REQUEST_URI'];
            $routeParams = explode('/', $params);
            $uriParams = explode('/', $route);

            if (count($uriParams) != count($routeParams)) {
                return false;
            }

            $count = 0;
            foreach ($uriParams as $key => $value) {
                $matching = preg_match("/{.*?}/", $value);
                if ($matching == 0) {
                    if ($routeParams[$count] != $uriParams[$count]) {
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
