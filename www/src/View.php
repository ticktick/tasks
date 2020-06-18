<?php

namespace Core;

use Core\Exception\ViewError;
use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;
use \Twig\Error\Error;

class View
{

    private $data = [];
    /* @var \Twig\TemplateWrapper */
    private $template;
    private $twig;

    public function __construct(array $config)
    {
        $loader = new FilesystemLoader($config['templates_path']);
        $this->twig = new Environment($loader);
    }

    /**
     * @param string $template
     * @return $this
     * @throws ViewError
     */
    public function setTemplate(string $template): View
    {
        try {
            $this->template = $this->twig->load($template);
        } catch (Error $e) {
            throw new ViewError($e->getMessage());
        }
        return $this;
    }

    public function setData(array $data): View
    {
        $this->data = $data;
        return $this;
    }

    public function getContent(): string
    {
        ob_start();
        echo $this->template->render($this->data);
        return ob_get_clean();
    }
}