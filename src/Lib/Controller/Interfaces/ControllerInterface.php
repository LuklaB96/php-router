<?php
namespace App\Lib\Controller\Interfaces;

interface ControllerInterface
{
    public function redirectToRoute(string $route, array $parameters = []);
    public function renderView(string $view, array $data = []);

}
?>