<?php
namespace App\Lib\Controller;

use App\Lib\Controller\Interfaces\ControllerInterface;
use App\Lib\View\View;

class BaseController implements ControllerInterface
{

    public function __construct()
    {
    }
    public function redirectToRoute(string $route, array $parameters = [])
    {

    }
    public function renderView(string $view, array $data = [])
    {
        View::render($view, $data);
    }

}
?>