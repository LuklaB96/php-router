<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Lib\Assets\AssetMapper;
use App\Entity\ExampleEntity;
use App\Lib\Database\Mapping\AttributeReader;
use App\Lib\PropAccessor\PropertyAccessor;
use App\Main\App;
use App\Lib\Routing\Router;
use App\Lib\Routing\Response;
use App\Lib\Routing\Request;
use App\Lib\View\View;
use App\Lib\Config;

//check if uri exists as a public file in /config/asset_mapper_config.php
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

$entity = new ExampleEntity();

$attrs = AttributeReader::getAttributes($entity);
$obj = [];
foreach ($attrs as $key) {
    $obj[] = AttributeReader::createColumn($key);
}
foreach ($obj as $o)
    echo json_encode($o) . '</br>';
//echo json_encode($attrs['id'], JSON_PRETTY_PRINT);
//every route is unique, if we make two identical endpoints, only first one will be executed.

//basic route
Router::get('/', function () {
    View::render('ExampleView', [
        'helloWorld' => 'Hello World!',
    ]);
});

Router::get('/{id}', function (Request $req) {
    echo $req->params->id;
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
    $message = $postEntity->insert($result);
    $res->toJSON([
        'message' => $message
    ]);
});

//check if any route has been set as valid, display error if not.
Router::check();

App::run();

?>