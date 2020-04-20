<?php

namespace Core;

class Validator
{

    /** @var ValidatorRule[] */
    public $rules = [];
    public $errors = [];

    public function addRule(ValidatorRule $rule): Validator
    {
        $this->rules[] = $rule;
        return $this;
    }

    public function validate()
    {
        foreach ($this->rules as $rule){
            if($error = $rule->getError()){
                $this->addError($rule->getName(), $error);
            }
        }
    }

    public function addError($name, $message)
    {
        $this->errors[$name] = $message;
    }

    public function isSuccess(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getRedirectQuery(): string
    {
        if ($this->isSuccess()) {
            $query = ['success' => 1];
        } else {
            $query = ['errors' => $this->getErrors()];
        }
        return http_build_query($query);
    }

}