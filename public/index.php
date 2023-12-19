<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Entity\Person;
use App\Lib\Assets\AssetMapper;
use App\Main\App;
use App\Lib\Routing\Router;
use App\Lib\Routing\Response;
use App\Lib\Routing\Request;
use App\Lib\View\View;
use App\Lib\Config;


//header('Content-Type: text/css; charset=UTF-8');
//readfile(__DIR__ . '/assets/styles/app.css');
//check if uri exists as a public file in or is in /config/asset_mapper.php
$isAsset = AssetMapper::isAsset();
if ($isAsset) {
    return false;
}

//helper function to inject assets into views
function asset($asset)
{
    $assets = Config::get('assets');
    echo $assets[$asset];
}
//can be used in views if we are sure that specific variable will be extracted. 
//it will simply insert targeted variable value into html document
function get($var)
{
    echo isset($var) ? $var : null;
}

//router instance, multiple instances are possible.
$router = Router::getInstance('default');
//every route is unique, if we make two identical endpoints, only first one will be executed.

//example routes
$router->get('/', function () {
    $person = new Person();
    $personRepository = $person->findAll();
    foreach ($personRepository as $p) {
        echo $p->getImie() . '</br>';
    }

    View::render('ExampleView', [
        'helloWorld' => 'Hello World!',
    ]);
});

$router->get('/phpinfo', function (Request $req) {
    echo apache_get_version();
});

$router->get('/blog', function () {
    $data = [
        'helloWorld' => 'Hello World!',
    ];
    View::render('ExampleView', $data);
});

$router->get('/post/{id}', function (Request $req, Response $res) {
    $res->toJSON([
        'post' => [
            'id' => $req->params->id,
        ],
        'status' => 'ok'
    ]);
});
$router->get('/person/{id}/{imie}/{nazwisko}', function (Request $req, Response $res) {
    $res->toJSON([
        'post' => [
            'id' => $req->params->id,
            'imie' => $req->params->imie,
            'nazwisko' => $req->params->nazwisko,
        ],
        'status' => 'ok'
    ]);
});

$router->get('/person/{id}', function (Request $req, Response $res) {
    $person = new Person();
    $person->find($req->params->id);
    if ($person->getId() == null) {
        $res->toJSON([
            'status' => 'not found'
        ]);
    } else {
        $res->toJSON([
            'person' => [
                'id' => $person->getId(),
                'imie' => $person->getImie(),
                'nazwisko' => $person->getNazwisko()
            ],
            'status' => 'ok'
        ]);
    }
});

//will only work when properly sent POST request.
$router->post('/test', function (Request $req, Response $res) {
    $result = $req->getData();
    $person = new Person();

    $person->setImie($result['imie']);
    $person->setNazwisko($result['nazwisko']);
    $message = $person->insert();
    $res->toJSON($message);
});

//check if any route has been set as valid, display error like 'page not found' or render specific view for this type of event.
$executed = $router->check();
if ($executed === false)
    View::render('Error404');

App::run();

?>