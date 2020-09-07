<?php
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
