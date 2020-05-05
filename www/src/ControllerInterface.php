<?php

namespace Core;

interface ControllerInterface
{

    public function init(): void;

    public function getView(): View;

}