<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Lib\Assets\AssetMapper;
use App\Main\App;
use App\Lib\Routing\Router;
use App\Lib\Routing\Response;
use App\Lib\Routing\Request;
use App\Lib\View\View;
use App\Lib\Config;

use App\Entity\Post;
use App\Lib\Migrations\Migrations;



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

//every route is unique, if we make two identical endpoints, only first one will be executed.

//basic route
Router::get('/', function () {

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
    $json = json_encode($result);
    echo $json;
});

//check if any route has been set as valid, display error if not.
Router::check();

App::run();

?>