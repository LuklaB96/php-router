<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Lib\Assets\AssetMapper;
use App\Lib\Database\Database;
use App\Lib\ErrorHandler\ErrorHandler;
use App\Lib\ExceptionHandler\ExceptionHandler;
use App\Lib\Security\CSRF\SessionTokenManager;
use App\Lib\Security\HTML\HiddenFieldGenerator;
use App\Lib\View\View;
use App\Main\App;
use App\Lib\Config;

//force HTTPS
requireSSL();

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
function renderBody(string $view, array $additionalData)
{
    if ($view !== '') {
        View::render($view, $additionalData);
        echo PHP_EOL;
    }

}
function renderScripts(array $scripts)
{
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
function requireSSL()
{
    $requireSSL = Config::get('REQUIRE_SSL', false);
    if ((empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] !== "on") && $requireSSL) {
        header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        exit();
    }
}
function checkDatabase()
{
    $db = Database::getInstance();
    if ($db->isConnected()) {
        $dbname = Config::get('DB_NAME', 'app_db');
        try {
            $db->execute("SELECT 1 FROM `$dbname`.`user`");
            return true;
        } catch (\Exception $e) {
            if ($e->getCode() == "42S02") {
                return false;
            }
            echo 'DATABASE ERROR';
            exit();
        }
    }
}
$dbok = checkDatabase();
if ($dbok) {
    App::run();
} else {
    $db = Database::getInstance();
    if ($db->isConnected()) {
        echo 'Creating migrations..</br>';
        include __DIR__ . '/../scripts/Database/database_create.php';
        include __DIR__ . '/../scripts/Database/migrations.php';
        echo '<a href="/">Refresh Page</a>';
    } else {
        echo 'Application not connected to database!';
        return;
    }
    session_destroy();
}
?>