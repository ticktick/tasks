<?php

namespace App\Controller;

use Core\Controller;
use Core\ControllerInterface;
use Core\Exception\HttpRedirect;

class Index extends Controller implements ControllerInterface
{

    /**
     * @throws HttpRedirect
     */
    public function indexAction()
    {
        $this->redirect('/task');
    }
}