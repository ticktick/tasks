<?php

namespace Core;

class Validator
{

    private $name;
    private $value;

    public $errors = [];

    public function name($name)
    {
        $this->name = $name;
        return $this;

    }

    public function value($value)
    {
        $this->value = $value;
        return $this;
    }

    public function addError($name, $value)
    {
        $this->errors[$name] = $value;
    }

    public function required()
    {
        if ($this->value == '' || $this->value == null) {
            $this->addError($this->name, 'Не задано поле ' . $this->name);
        }
        return $this;
    }

    public function isEmail()
    {
        if (filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            return $this;
        }
        $this->addError($this->name, 'Неверный e-mail ' . $this->value);
        return $this;
    }

    public function equalsString(string $str)
    {
        if (strcmp($this->value, $str) != 0) {
            $this->addError($this->name, 'Неверное значение ' . $this->name);
        }
        return $this;
    }

    public function isSuccess()
    {
        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getRedirectQuery()
    {
        if ($this->isSuccess()) {
            $query = ['success' => 1];
        } else {
            $query = ['errors' => $this->getErrors()];
        }
        return http_build_query($query);
    }

}