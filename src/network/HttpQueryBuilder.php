<?php

namespace skygoose\backend_framework\network;

use skygoose\backend_framework\routes\enums\ApiMethod;
use skygoose\backend_framework\routes\enums\Method;

final class HttpQueryBuilder
{
    private \CurlHandle $ch;
    private string $uri;
    private array $payload = [];
    private int $method;

    public function __construct(string $uri)
    {
        $this->uri = $uri;
        $this->ch = curl_init();

        curl_setopt($this->ch, CURLOPT_URL, $uri);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }

    public function setPayload(array $data) : self {
        $this->payload = $data;
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        return $this;
    }

    public function setMethod(Method $method) : self {
        curl_setopt($this->ch, ApiMethod::adaptToCurl($method), 1);
        return $this;
    }

    public function build() : string|bool {
        $result = curl_exec($this->ch);
        curl_close($this->ch);

        return $result;
    }
}