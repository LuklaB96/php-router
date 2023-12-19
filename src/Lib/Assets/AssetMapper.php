<?php
namespace App\Lib\Assets;

use App\Lib\Config;

class AssetMapper
{
    /**
     * First check if route is trying to access public asset in /public/assets/
     * Then check if file is set to public in /config/asset_mapper.php
     *
     * @return bool
     */
    public static function isAsset(): bool
    {
        //first check if asset is public, so we dont need to check asset_mapper.php if true
        $isAsset = self::isPublicAsset();
        if ($isAsset) {
            return true;
        }

        $isAsset = self::isPublicFile();
        if ($isAsset) {
            return true;
        }


        return false;

    }
    /**
     * Checks if route is trying to access specific file, public files are configured in /assets/asset_mapper.php
     *
     * @return bool
     */
    public static function isPublicFile(): bool
    {
        $uri = $_SERVER["REQUEST_URI"];
        $assets = Config::get('assets');
        $url1 = Config::get('MAIN_DIR') . $uri;
        $pathInfo = pathinfo($url1);
        //check if our route ends with file.extension
        if (isset($pathInfo['extension'])) {
            //check if file.extension is avaible as asset
            if (array_key_exists($pathInfo['basename'], $assets)) {
                $url2 = Config::get('MAIN_DIR') . $assets[$pathInfo['basename']];

                //check if asset url is the same as asset path, case-sensitive, asset/styles/app.css == asset/styles/APP.CSS
                return strcasecmp($url1, $url2) === 0 ? true : false;
            }
            return false;
        }
        return false;
    }
    /**
     * Checks if route is trying to access /public/assets/ folder
     *
     * @return bool
     */
    public static function isPublicAsset(): bool
    {
        $uri = $_SERVER["REQUEST_URI"];
        $path = Config::get('MAIN_DIR') . $uri;
        //split uri into params
        $uriParams = explode('/', $uri);
        //first param is empty so we can get rid of it
        array_shift($uriParams);
        //check if file exists and if given path is correct and pointing to /public/assets/ folder.
        if (file_exists($path) && strcasecmp($uriParams[0], 'public') === 0 && strcasecmp($uriParams[1], 'assets') === 0) {
            return true;
        }
        return false;
    }
}
