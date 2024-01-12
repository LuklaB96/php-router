<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Entity\Person;
use App\Lib\Assets\AssetMapper;
use App\Lib\Database\Mapping\AttributeReader;
use App\Lib\ErrorHandler\ErrorHandler;
use App\Lib\ExceptionHandler\ExceptionHandler;
use App\Lib\Security\CSRF\SessionTokenManager;
use App\Lib\Security\HTML\HiddenFieldGenerator;
use App\Main\App;
use App\Lib\Config;

//create global session
createSession();

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
function createSession()
{
    $sessionTokenManager = SessionTokenManager::getInstance();
}
function renderBody(string $view)
{
    error_log('render_body');
    include Config::get('MAIN_DIR') . '/src/Views/' . $view . '.php';
    echo PHP_EOL;
}
function renderScripts(array $scripts)
{
    error_log('render_scripts');
    if (isset($scripts) && !empty($scripts)) {
        foreach ($scripts as $script) {
            if (!empty($script['type']) && !empty($script['path'])) {
                echo '<script type="' . $script['type'] . '" src="' . $script['path'] . '"></script>' . PHP_EOL;
            } else {
                // Handle the case where either 'type' or 'path' is empty
                echo '<!-- Invalid script data -->';
            }
        }
    }
}

function renderStyles(array $styles)
{
    error_log('render_styles');
    if (isset($styles) && !empty($styles)) {
        foreach ($styles as $style) {
            if (!empty($style)) {
                echo '<link rel="stylesheet" type="text/css" href="' . $style . '">' . PHP_EOL;
            } else {
                // Handle the case where a style path is empty
                echo '<!-- Invalid style data -->';
            }
        }
    }
}

function renderHead(string $headHTML)
{
    if (isset($headHTML)) {
        echo $headHTML . PHP_EOL;
    }
}

App::run();
?>