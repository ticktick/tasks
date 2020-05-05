<?php

namespace Core;

use Core\Exception\ControllerNotExists;
use Core\Exception\ActionNotExists;
use Core\Exception\HttpRedirect;

class Application
{
    /** @var Request */
    private $request;
    /** @var array */
    private $config;
    /** @var ModelFactory */
    private $modelFactory;

    public function __construct(array $config)
    {
        $this->setConfig($config);
        $this->setRequest(new Request($config));

        $modelFactory = new ModelFactory($config['database']);
        $this->setModelFactory($modelFactory);
    }

    public function run(): bool
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

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setModelFactory(ModelFactory $modelFactory): void
    {
        $this->modelFactory = $modelFactory;
    }

    public function getModelFactory(): ModelFactory
    {
        return $this->modelFactory;
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
        return new $controllerClassName($this->getRequest(), $this->getConfig(), $this->getModelFactory());
    }

    private function render($content): bool
    {
        echo $content;
        return true;
    }

    private function getActionName(): string
    {
        return $this->getRequest()->getAction() . 'Action';
    }
}