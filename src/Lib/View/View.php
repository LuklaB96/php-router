<?php

namespace App\Lib\View;

use App\Lib\Assets\AssetMapper;
use App\Lib\Config;

class View
{
    private string $layout;
    private string $view = '';
    private string $title = '';
    private array $styles = [];
    private array $scripts = [];
    private string $headHTML = '';
    public function __construct(string $view, string $title = '', string $layout = 'layout')
    {
        $this->view = $view;
        $this->title = $title;
        $this->layout = $layout;
    }
    /**
     * Render full view file.
     * @param string $view
     * @return void
     */
    public static function render(string $view, array $data = [])
    {
        if (!empty($data)) {
            extract($data);
        }
        include Config::get('MAIN_DIR') . '/src/Views/' . $view . '.php';
    }
    /**
     * render body into existing layout
     * @return void
     */
    public function renderPartial(array $additionalData = [])
    {
        $data = [];
        if (!empty($additionalData)) {
            $data = array_merge($data, $additionalData);
        }
        if (!empty($this->view)) {
            $data['view'] = $this->view;
        }
        if (!empty($this->title)) {
            $data['title'] = $this->title . PHP_EOL;
        }
        if (!empty($this->styles)) {
            $data['styles'] = $this->styles;
        }
        if (!empty($this->scripts)) {
            $data['scripts'] = $this->scripts;
        }
        if (!empty($this->headHTML)) {
            $data['headHTML'] = $this->headHTML . PHP_EOL;
        }
        if (!empty($data)) {
            extract($data);
        }

        self::render($this->layout, $data);
    }
    public function addStyle(string $asset)
    {
        $assets = Config::get('ASSETS');
        $assetPath = AssetMapper::getRootDir() . $assets[$asset];
        $this->styles[] = $assetPath;
    }

    public function addScript(string $asset, string $type)
    {
        $assets = Config::get('ASSETS');
        $assetPath = AssetMapper::getRootDir() . $assets[$asset];
        $this->scripts[] = ['path' => $assetPath, 'type' => $type];
    }

    public function setHeadHTML(string $headHTML)
    {
        $this->headHTML = $headHTML;
    }
}

?>