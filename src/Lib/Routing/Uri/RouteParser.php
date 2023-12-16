<?php
namespace App\Lib\Routing\Uri;

class RouteParser
{
    /**
     * Returns params array specified in Router if passed correctly to $_SERVER['REQUEST_URI'], check if route is valid before using.
     * @param string $route
     * @return array
     */
    public static function getRouteParams(string $route): array
    {

        $uriParams = $_SERVER['REQUEST_URI'];

        //split all params from route and uri into arrays
        $paramValues = explode('/', $uriParams);
        $routeParams = explode('/', $route);


        //check if route has specified parameters
        preg_match_all('/{(.*?)}/', $route, $matches, PREG_PATTERN_ORDER);


        //get all keys from regex matches
        $paramKeys = [];
        foreach ($matches as $match) {
            $count = 0;
            foreach ($match as $key => $value) {
                $paramKeys[$count] = $value;
                $count++;
            }
        }


        //get position of all true parameters passed by user in array
        $paramPositions = [];
        $count = 0;
        foreach ($routeParams as $key => $value) {
            $matching = preg_match("/{.*?}/", $value);
            if ($matching) {
                $paramPositions[] = $count;
            }
            $count++;
        }

        //create params object with key => value structure, where key is a route parameter name inside curly brackets {} and value is taken directly from uri.
        if (!empty($paramKeys) && !empty($paramPositions) && !empty($paramValues)) {
            $params = [];
            for ($i = 0; $i < count($paramKeys); $i++) {
                $params[$paramKeys[$i]] = $paramValues[$paramPositions[$i]];
            }
            return $params;
        }
        return [];
    }
}