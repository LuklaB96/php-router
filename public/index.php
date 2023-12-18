<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Lib\Assets\AssetMapper;
use App\Entity\ExampleEntity;
use App\Lib\Assets\RenderAsset;
use App\Lib\Database\Mapping\AttributeReader;
use App\Lib\PropAccessor\PropertyAccessor;
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
    $entity = new ExampleEntity();
    $entity->find(13);
    echo $entity->getDescription();
    $entity->setDescription('set description and update');
    $entity->update();

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

//will only work when properly sent POST request.
Router::post('/test', function (Request $req, Response $res) {
    $result = $req->getData();
    $postEntity = new ExampleEntity();
    $postEntity->setTitle($result['title']);
    $postEntity->setDescription($result['description']);
    $message = $postEntity->insert();
    $res->toJSON($message);
});

//check if any route has been set as valid, display error if not.
Router::check();

App::run();

?>