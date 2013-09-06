<?php

namespace Web\Route;

use Depend\Manager;
use Web\Route\Abstraction\DependencyContainerInterface;

class DependManagerProxy implements DependencyContainerInterface
{
    /**
     * @var Manager
     */
    protected $dm;

    /**
     * @param Manager $dm
     */
    function __construct(Manager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * Get an instance of the given className or alias
     *
     * @param string $name
     *
     * @return object
     */
    public function get($name)
    {
        return $this->dm->get($name);
    }
}
