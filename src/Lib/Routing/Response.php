<?php

namespace App\Lib\Routing;

class Response
{
    private $status = 200;

    public function status(int $code)
    {
        $this->status = $code;
        return $this;
    }
    /**
     * Simple json response with default header and response code.
     *
     * @param  mixed $data
     * @return void
     */
    public function toJSON($data = [])
    {
        http_response_code($this->status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_PRETTY_PRINT);
    }
}
