<?php

namespace Core;

use Core\Exception\ViewError;

class View
{

    private $data = [];
    /* @var \Twig\TemplateWrapper */
    private $template;
    private $twig;

    public function __construct()
    {
        // @TODO move to config
        $loader = new \Twig\Loader\FilesystemLoader('/var/www/app/templates');
        $this->twig = new \Twig\Environment($loader);
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
        } catch (\Twig\Error\Error $e) {
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