<?php

namespace Core;

use Core\Exception\ControllerNotExists;
use Core\Exception\ActionNotExists;
use Core\Exception\HttpRedirect;

class Application
{
    /** @var string */
    private $content = '';
    /** @var Request */
    private $request;
    /** @var array */
    private $config;
    /** @var View */
    private $view;
    /** @var ModelFactory */
    private $modelFactory;

    public function __construct(array $config)
    {
        $this->setConfig($config);
        $this->setRequest(new Request($config));
        $this->setView(new View($config['view']));
        $modelFactory = new ModelFactory($config['database']);
        $this->setModelFactory($modelFactory);
    }

    public function run(): bool
    {
        try {
            $controller = $this->getController();
            $this->runAction($controller, $this->getActionName());
            $view = $controller->getView();
            $content = $view->getContent();
        } catch (HttpRedirect $e) {
            return $this->redirect($e);
        } catch (ControllerNotExists | ActionNotExists $e) {
            $this->notFoundError($e);
            $content = 'Страница не найдена';
        } catch (\Exception $e) {
            $this->internalError($e);
            $content = 'Произошла внутренняя ошибка.';
        }

        $this->setContent($content);
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

    public function setView(View $view): void
    {
        $this->view = $view;
    }

    public function getView(): View
    {
        return $this->view;
    }

    public function setModelFactory(ModelFactory $modelFactory): void
    {
        $this->modelFactory = $modelFactory;
    }

    public function getModelFactory(): ModelFactory
    {
        return $this->modelFactory;
    }

    private function setContent($content): void
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
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
        return new $controllerClassName($this);
    }

    /**
     * @param ControllerInterface $controller
     * @param string $actionName
     * @throws ActionNotExists
     */
    private function runAction(ControllerInterface $controller, string $actionName): void
    {
        if (!$this->isActionExists($controller, $this->getActionName())) {
            throw new ActionNotExists();
        }
        $controller->{$actionName}();
    }

    private function isActionExists(ControllerInterface $controller, string $actionName): bool
    {
        return method_exists($controller, $actionName);
    }

    private function setHeader(string $header)
    {
        header($header);
    }

    private function redirect(HttpRedirect $e): bool
    {
        $this->setHeader('location: ' . $e->getUrl());
        return true;
    }

    private function notFoundError(\Exception $e): void
    {
        $this->setHeader('HTTP/1.1 404 Not found');
        $this->logWarning($e);
    }

    private function internalError(\Exception $e): void
    {
        $this->setHeader('HTTP/1.1 500 Internal Server Error');
        $this->logWarning($e);
    }

    private function logWarning(\Exception $e)
    {
        trigger_error($e->getMessage(), E_USER_WARNING);
    }

    private function getActionName(): string
    {
        return $this->getRequest()->getAction() . 'Action';
    }
}