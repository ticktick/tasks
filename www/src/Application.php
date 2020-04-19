<?php

namespace Core;

use Core\Exception\ControllerNotExists;
use Core\Exception\ActionNotExists;
use Core\Exception\HttpRedirect;

class Application
{
    /** @var Request */
    private $request;

    public function __construct($config)
    {
        $this->setRequest(new Request());
    }

    public function run()
    {
        try {
            $controller = $this->getController();
            $controller->init();

            if (!method_exists($controller, $this->getActionName())) {
                throw new ActionNotExists();
            }

            $controller->{$this->getActionName()}();
            $view = $controller->getView();
            $content = $view->getContent();
        } catch (HttpRedirect $e) {
            header('location: ' . $e->getUrl());
            return true;
        } catch (ControllerNotExists | ActionNotExists $e) {
            header('HTTP/1.1 404 Not found');
            trigger_error($e->getMessage(), E_USER_WARNING);
            $content = 'Страница не найдена';
        } catch (\Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            trigger_error($e->getMessage(), E_USER_WARNING);
            $content = 'Произошла внутренняя ошибка.';
        }

        $this->render($content);
        if (!empty($bottomContent)) {
            $this->render($bottomContent);
        }
        return true;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @throws ControllerNotExists
     */
    private function getController(): ControllerInterface
    {
        $controllerClassName = 'App\\Controller\\' . ucfirst($this->getRequest()->getResource());
        if (!class_exists($controllerClassName)) {
            throw new ControllerNotExists();
        }
        return new $controllerClassName($this->getRequest());
    }

    private function render($content)
    {
        echo $content;
        return true;
    }

    private function getActionName()
    {
        return $this->getRequest()->getAction() . 'Action';
    }
}