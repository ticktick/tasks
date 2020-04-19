<?php

namespace Core\Exception;

class HttpRedirect extends \Exception
{

    private $url;

    public function __construct(string $url)
    {
        parent::__construct();
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}