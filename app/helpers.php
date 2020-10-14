<?php

use Illuminate\Support\Str;

if (!function_exists('run_action')) {
    /**
     * @param $actionClass
     * @param $arguments
     * @return mixed
     */
    function run_action($actionClass, ...$arguments)
    {
        $object = is_object($actionClass)
            ? $actionClass
            : app($actionClass);

        return call_user_func([$object, 'execute'], ...$arguments);
    }
}

if (!function_exists('to_url')) {
    function to_url(string $string): string
    {
        $kebab = Str::kebab(trim($string));
        return  preg_replace("/[^a-zA-Z0-9-]+/", "", $kebab);
    }
}
