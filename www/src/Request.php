<?php

namespace Core;

class Request
{

    private $resource;
    private $action;

    const ADMIN_COOKIE_NAME = 'auth';
    const ADMIN_COOKIE_HASH = '6756088577abe3c76de3cf1bb0c04991';

    public function __construct()
    {
        list($resourceAndAction) = explode('?', $_SERVER['REQUEST_URI']);
        $requestUri = explode("/", $resourceAndAction);
        $this->resource = !empty($requestUri[1]) ? strtolower($requestUri[1]) : 'index';
        $this->action = !empty($requestUri[2]) ? strtolower($requestUri[2]) : 'index';
    }

    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function makeAdmin()
    {
        setcookie(self::ADMIN_COOKIE_NAME, self::ADMIN_COOKIE_HASH, time() + 86400, '/');
    }

    public function revokeAdmin()
    {
        unset($_COOKIE[self::ADMIN_COOKIE_NAME]);
        setcookie(self::ADMIN_COOKIE_NAME, null, -1, '/');
    }

    public function isAdmin()
    {
        return isset($_COOKIE[self::ADMIN_COOKIE_NAME]) && $_COOKIE[self::ADMIN_COOKIE_NAME] == self::ADMIN_COOKIE_HASH;
    }

    public function p($param = null, $default = null)
    {
        if (is_null($param)) {
            return $_REQUEST;
        }
        return $_REQUEST[$param] ?? $default;
    }

    public function get($param = null, $default = null)
    {
        if (is_null($param)) {
            return $_GET;
        }
        return $_GET[$param] ?? $default;
    }

    public function post($param = null, $default = null)
    {
        if (is_null($param)) {
            return $_POST;
        }
        return $_POST[$param] ?? $default;
    }
}