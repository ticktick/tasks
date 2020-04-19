<?php

namespace Core;

use Core\Exception\HttpRedirect;

abstract class Controller implements ControllerInterface
{

    /** @var Request */
    protected $request;
    /** @var View */
    public $view;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function init()
    {
        $this->setView(new View());
    }

    public function setView(View $view)
    {
        $this->view = $view;
    }

    public function getView(): View
    {
        return $this->view;
    }

    /**
     * @param $url
     * @throws HttpRedirect
     */
    public function redirect($url)
    {
        throw new HttpRedirect($url);
    }
}