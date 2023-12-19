<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Entity\Person;
use App\Lib\Assets\AssetMapper;
use App\Entity\ExampleEntity;
use App\Main\App;
use App\Lib\Routing\Router;
use App\Lib\Routing\Response;
use App\Lib\Routing\Request;
use App\Lib\View\View;
use App\Lib\Config;

//check if uri exists as a public file in /config/asset_mapper_config.php
//header('Content-Type: text/css; charset=UTF-8');
//readfile(__DIR__ . '/assets/styles/app.css');

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
//every route is unique, if we make two identical endpoints, only first one will be executed.

//basic route
Router::get('/', function () {
    $person = new Person();
    $person->find(1);
    echo $person->getImie();

    View::render('ExampleView', [
        'helloWorld' => 'Hello World!',
    ]);
});

Router::get('/phpinfo', function (Request $req) {
    echo apache_get_version();
});

//example route with data extracted to view
//this view can send post request through form to /test
Router::get('/blog', function () {
    $data = [
        'helloWorld' => 'Hello World!',
    ];
    View::render('ExampleView', $data);
});

//example route using Response class
Router::get('/post/{id}', function (Request $req, Response $res) {
    $res->toJSON([
        'post' => [
            'id' => $req->params->id,
        ],
        'status' => 'ok'
    ]);
});
Router::get('/person/{id}/{imie}/{nazwisko}', function (Request $req, Response $res) {
    $res->toJSON([
        'post' => [
            'id' => $req->params->id,
            'imie' => $req->params->imie,
            'nazwisko' => $req->params->nazwisko,
        ],
        'status' => 'ok'
    ]);
});

Router::get('/person/{id}', function (Request $req, Response $res) {
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
Router::post('/test', function (Request $req, Response $res) {
    $result = $req->getData();
    $person = new Person();

    $person->setImie($result['imie']);
    $person->setNazwisko($result['nazwisko']);
    $message = $person->insert();
    $res->toJSON($message);
});

//check if any route has been set as valid, display error if not.
Router::check();

App::run();

?>