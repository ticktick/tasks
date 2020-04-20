<?php

namespace App\Controller;

use Core\Controller;
use Core\ControllerInterface;
use Core\Exception\HttpRedirect;
use Core\Validator;
use Core\ValidatorRule;

class User extends Controller implements ControllerInterface
{

    // @TODO move to config
    const ADMIN_NAME = 'admin';
    const ADMIN_PASSWORD = '123';

    /**
     * @return string
     * @throws HttpRedirect
     * @throws \Core\Exception\ViewError
     */
    public function loginAction()
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
    public function authAction()
    {
        $request = $this->request;
        $auth = false;

        $validator = new Validator();

        $validator
            ->addRule((new ValidatorRule('Имя', $this->request->post('name')))->equalsString(self::ADMIN_NAME)->required())
            ->addRule((new ValidatorRule('Пароль', $this->request->post('password')))->equalsString(self::ADMIN_PASSWORD)->required())
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
    public function logoutAction()
    {
        $this->request->revokeAdmin();
        $this->redirect('/task');
    }
}