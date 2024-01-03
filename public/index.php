<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Lib\Assets\AssetMapper;
use App\Lib\ErrorHandler\ErrorHandler;
use App\Lib\ExceptionHandler\ExceptionHandler;
use App\Lib\Security\CSRF\SessionTokenManager;
use App\Lib\Security\HTML\HiddenFieldGenerator;
use App\Main\App;
use App\Lib\Config;

// error/exception handlers
$errorHandler = function ($errno, $errstr, $errfile, $errline) {
    (new ErrorHandler())->handle($errno, $errstr, $errfile, $errline);
};
set_error_handler($errorHandler);

$exceptionHandler = function ($exception) {
    (new ExceptionHandler())->handle($exception);
};
set_exception_handler($exceptionHandler);

// check if uri exists as a public file or is in /config/asset_mapper.php
$isAsset = AssetMapper::isAsset();
if ($isAsset) {
    return false;
}
/**
 * Support function for valid asset path injection into views, configuration & more info in /config/asset_mapper.php
 * @param mixed $asset
 * @return void
 */
function asset($asset)
{
    $assets = Config::get('ASSETS');
    echo AssetMapper::getRootDir() . $assets[$asset];
}
/**
 * used in views to display extracted variable value
 * @param mixed $var
 * @return void
 */
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
    $isValidToken = $sessionTokenManager->validateToken($sessionTokenManager->getToken());
    if (!$isValidToken) {
        $sessionTokenManager->regenerateToken();
    }
    $hiddenField = HiddenFieldGenerator::generate('token', $sessionTokenManager->getToken());
    echo $hiddenField;
}

App::run();
?>