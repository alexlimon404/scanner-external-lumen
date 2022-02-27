<?php

namespace App\Actions;

abstract class Action
{
    public static function run()
    {
        $action = new static(...func_get_args());

        return $action->handle();
    }
}