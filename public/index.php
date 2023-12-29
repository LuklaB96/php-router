<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Lib\Assets\AssetMapper;
use App\Lib\Security\CSRF\SessionTokenManager;
use App\Lib\Security\HTML\HiddenFieldGenerator;
use App\Main\App;
use App\Lib\Config;


//check if uri exists as a public file or is in /config/asset_mapper.php
$isAsset = AssetMapper::isAsset();
if ($isAsset) {
    return false;
}
//helper function to inject assets into views, they must exist in /config/asset_mapper.php
function asset($asset)
{
    $assets = Config::get('assets');
    echo $assets[$asset];
}
//can be used in views if we are sure that specific variable will be extracted.
function get($var)
{
    echo isset($var) ? $var : null;
}

/**
 * This will put hidden input field in form that is sending post request, check src/Views/ExampleView.php for more info
 * @return void
 */
function HiddenCSRF()
{
    $sessionTokenManager = SessionTokenManager::getInstance();
    $hiddenField = HiddenFieldGenerator::generate('token', $sessionTokenManager->getToken());
    echo $hiddenField;
}

App::run();
?>