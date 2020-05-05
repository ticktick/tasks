<?php

namespace App\Controller;

use Core\Controller;
use Core\ControllerInterface;
use Core\Exception\HttpRedirect;
use Core\Validator;
use Core\ValidatorRule;

class User extends Controller implements ControllerInterface
{

    /**
     * @return string
     * @throws HttpRedirect
     * @throws \Core\Exception\ViewError
     */
    public function loginAction(): string
    {
        $request = $this->request;
        $auth = $request->isAdmin();

        if ($auth) {
            $this->redirect('/task');
        }

        $this->getView()->setData([
            'errors' => $this->request->get('errors', []),
        ]);

        return $this->getView()
            ->setTemplate('user.login.html')
            ->getContent();
    }

    /**
     * @throws HttpRedirect
     */
    public function authAction(): void
    {
        $request = $this->request;
        $auth = false;

        $adminName = $this->config['admin']['name'];
        $adminPassword = $this->config['admin']['password'];

        $validator = new Validator();
        $validator
            ->addRule((new ValidatorRule('Имя', $this->request->post('name')))->equalsString($adminName)->required())
            ->addRule((new ValidatorRule('Пароль', $this->request->post('password')))->equalsString($adminPassword)->required())
            ->validate();

        if ($validator->isSuccess()) {
            $request->makeAdmin();
            $auth = true;
        }

        if ($auth) {
            $this->redirect('/task');
        } else {
            $this->redirect('/user/login?' . $validator->getRedirectQuery());
        }
    }

    /**
     * @throws HttpRedirect
     */
    public function logoutAction(): void
    {
        $this->request->revokeAdmin();
        $this->redirect('/task');
    }
}