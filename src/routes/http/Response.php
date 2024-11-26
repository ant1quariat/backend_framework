<?php

namespace skygoose\backend_framework\routes\http;

use skygoose\backend_framework\utils\BasicConstants;
use skygoose\backend_framework\utils\ReflectUtils;

final class Response
{
    private int $code;
    private array $data = [];
    private array $headers = [];

    public function __construct() {}


    /**
     * @param int $code
     */
    public function setCode(int $code): Response
    {
        $this->code = $code;
        return $this;
    }


    /**
     * @param array $data
     */
    public function setData(array $data): Response
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers): Response
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @param string $key
     * @param string $val
     */
    public function addHeader(string $key, string $val): Response
    {
        $this->headers[$key] = $val;
        return $this;
    }

    public function buildAsJson() {
        header(sizeof($this->headers) >= 1 ? implode("\r\n", $this->headers) : "Content-Type: text/json");
        http_response_code($this->code);

        echo json_encode($this->data ?: [], BasicConstants::getFlags());
        exit();
    }

    public static function ofJSON(int $status, array $data, array $headers = []): void {
        (new self())->setCode($status)->setData($data)->setHeaders($headers)->buildAsJson();
    }

    public static function ofHTML(int $status, string $html, array $headers = []): void {
        header(sizeof($headers) >= 1 ? implode("\r\n", $headers) : "Content-Type: text/html");
        http_response_code($status);

        echo $html;
        exit();
    }

}