<?php

namespace Core;

interface ControllerInterface
{

    public function init();

    public function getView(): View;

}