<?php

namespace App\Lib\Routing;

class Request
{
    public $reqMethod;
    public $contentType;
    private $isJson = false;

    public function __construct()
    {
        $this->reqMethod = trim($_SERVER['REQUEST_METHOD']);
        $this->contentType = !empty($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        //check if content type is json
        if (strcasecmp($this->contentType, 'application/json') === 0) {
            $this->isJson = true;
        }
    }

    /**
     * Filters all data in $_POST array and gets only data sent by POST method.
     *
     * @return array|string
     */
    public function getData()
    {
        //check if method is not POST.
        if ($this->reqMethod !== 'POST') {
            return '';
        }

        $data = [];
        //filter all data in $_POST variable, get only data sent by POST method.
        if (!$this->isJSON()) {
            foreach ($_POST as $key => $value) {
                $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        } else {
            $data = $this->getJSON();
        }


        return $data;
    }
    /**
     * Returns data sent with contentType header set as 'application/json', decode data into array and return.
     * 
     * Docs: https://www.php.net/manual/en/wrappers.php.php#wrappers.php.input
     *
     * @return mixed
     */
    public function getJSON()
    {
        //check if method is not POST.
        if ($this->reqMethod !== 'POST') {
            return [];
        }

        //check content type
        if (strcasecmp($this->contentType, 'application/json') !== 0) {
            return [];
        }

        //Receive the RAW post data.
        $content = trim(file_get_contents("php://input"));
        $decoded = json_decode($content, true);

        return $decoded;
    }
    public function isJSON(): bool
    {
        return $this->isJson;
    }
}