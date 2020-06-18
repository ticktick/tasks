<?php

namespace Core;

use Core\Exception\HttpRedirect;

abstract class Controller implements ControllerInterface
{

    /** @var Request */
    protected $request;
    /** @var array */
    protected $config;
    /** @var ModelFactory */
    protected $modelFactory;
    /** @var View */
    public $view;

    public function __construct(Application $application)
    {
        $this->request = $application->getRequest();
        $this->config = $application->getConfig();
        $this->modelFactory = $application->getModelFactory();
        $this->view = $application->getView();
    }

    public function getView(): View
    {
        return $this->view;
    }

    /**
     * @param $url
     * @throws HttpRedirect
     */
    public function redirect($url): void
    {
        throw new HttpRedirect($url);
    }
}