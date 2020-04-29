<?php

namespace Core;

class ValidatorRule
{

    /** @var string */
    public $error = null;
    /** @var string */
    private $name;
    private $value;

    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getError()
    {
        return $this->error;
    }

    public function required(): ValidatorRule
    {
        if ($this->value == '' || $this->value == null) {
            $this->error = 'Не задано поле ' . $this->name;
        }
        return $this;
    }

    public function isEmail(): ValidatorRule
    {
        if (filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            return $this;
        }
        $this->error = 'Неверный e-mail ' . $this->value;
        return $this;
    }

    public function equalsString(string $str): ValidatorRule
    {
        if (strcmp($this->value, $str) != 0) {
            $this->error = 'Неверное значение ' . $this->name;
        }
        return $this;
    }

    public function inList(array $values): ValidatorRule
    {
        if (!in_array($this->value, $values)) {
            $this->error = 'Недопустимое значение ' . $this->name;
        }
        return $this;
    }

    public function onlyFields(array $fields): ValidatorRule
    {
        $excess = array_diff(array_keys($this->value), $fields);
        if (count($excess)) {
            $this->error = 'Недопустимые поля';
        }
        return $this;
    }

}