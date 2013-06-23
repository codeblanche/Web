<?php

namespace Web\Route\Abstraction;

interface DependencyContainerInterface 
{
    /**
     * Get an instance of the given className or alias
     *
     * @param string $name
     *
     * @return object
     */
    public function get($name);
}
