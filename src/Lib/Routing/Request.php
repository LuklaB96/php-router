<?php

namespace App\Lib\Routing;

class Request
{
    public $params;
    public $reqMethod;
    public $contentType;

    public function __construct($params = null)
    {
        $this->params = $params;
        $this->reqMethod = trim($_SERVER['REQUEST_METHOD']);
        $this->contentType = !empty($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    }

    /**
     * Filters all data in $_POST array and gets only data sent by POST method.
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
        foreach ($_POST as $key => $value) {
            $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        return $data;
    }
    /**
     * Returns data sent with contentType header set as 'application/json', decode data into array and return.
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
        $decoded = json_decode($content);

        return $decoded;
    }
}