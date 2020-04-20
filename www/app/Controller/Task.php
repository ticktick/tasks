<?php

namespace App\Controller;

use Core\Controller;
use Core\ControllerInterface;
use Core\Exception\HttpRedirect;
use Core\Paginator;
use Core\Validator;
use Core\ValidatorRule;

class Task extends Controller implements ControllerInterface
{

    /**
     * @return string
     * @throws \Core\Exception\DatabaseError
     * @throws \Core\Exception\ModelTableNotDefined
     * @throws \Core\Exception\ViewError
     */
    public function indexAction()
    {
        $taskModel = new \App\Model\Task();

        // @TODO move to config
        $itemsPerPage = 3;

        $sort = $this->request->get('sort', 'id');
        $ord = $this->request->get('ord', Paginator::ORDER_ASC);
        $page = $this->request->get('page', 1);

        $paginator = new Paginator($itemsPerPage);
        $paginator->setSortField($sort);
        $paginator->setOrder($ord);
        $paginator->setPage($page);
        $paginator->setUrl('/task');

        $taskModel->setSortField($sort);
        $taskModel->setSortOrder($ord);
        $tasksCount = $taskModel->count();
        $tasks = $taskModel->find($page, $itemsPerPage);

        $paginator->setCount($tasksCount);

        $this->getView()->setData([
            'tasks' => $tasks,
            'errors' => $this->request->get('errors', []),
            'success' => $this->request->get('success', null),
            'pages' => $paginator->getPages(),
            'sort_buttons' => $paginator->getSortButtons(),
            'is_admin' => $this->request->isAdmin()
        ]);

        return $this->getView()
            ->setTemplate('task.index.html')
            ->getContent();
    }

    /**
     * @throws HttpRedirect
     * @throws \Core\Exception\ModelTableNotDefined
     */
    public function addAction()
    {
        $taskModel = new \App\Model\Task();

        $validator = new Validator();

        $validator
            ->addRule((new ValidatorRule('Поля', $this->request->post()))->onlyFields(['user_name', 'email', 'text']))
            ->addRule((new ValidatorRule('Имя', $this->request->post('user_name')))->required())
            ->addRule((new ValidatorRule('E-mail', $this->request->post('email')))->isEmail()->required())
            ->addRule((new ValidatorRule('Текст задачи', $this->request->post('text')))->required())
            ->validate();

        if ($validator->isSuccess()) {
            $taskModel->add($this->request->post());
        }

        $this->redirect('/task?' . $validator->getRedirectQuery());
    }

    /**
     * @return string
     * @throws HttpRedirect
     * @throws \Core\Exception\ModelTableNotDefined
     * @throws \Core\Exception\ViewError
     */
    public function changeAction()
    {
        $taskModel = new \App\Model\Task();

        $id = $this->request->p('id');

        if (!$id || !$this->request->isAdmin()) {
            $this->redirect('/task');
        }

        $task = $taskModel->findById($id);
        $data = $this->request->post();

        if ($task && !empty($data)) {
            $data['status'] = isset($data['status']) ? \App\Model\Task::STATUS_DONE : \App\Model\Task::STATUS_TODO;

            $textChangedRule = (new ValidatorRule('Текст', $data['text']))->equalsString($task['text']);
            $textChanged = (bool)$textChangedRule->getError();

            if ($textChanged) {
                $data['admin_fixed'] = true;
            }
            $taskModel->update($data);
            $this->redirect('/task');
        }

        $this->getView()->setData([
            'task' => $task,
            'change' => true,
        ]);

        return $this->getView()
            ->setTemplate('task.change.html')
            ->getContent();
    }
}