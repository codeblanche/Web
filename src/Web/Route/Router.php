<?php

namespace Web\Route;

use Web\Route\Abstraction\DependencyContainerInterface;

class Router
{
    /**
     * @var string
     */
    protected $catchallController;

    /**
     * @var DependencyContainerInterface
     */
    protected $dependencyManager;

    /**
     * @var array
     */
    protected $matchParams = array();

    /**
     * @var Rule
     */
    protected $rulePrototype;

    /**
     * @var Rule[]
     */
    protected $rules = array();

    /**
     * @param DependencyContainerInterface $dependencyManager
     * @param Rule                         $rulePrototype
     */
    public function __construct(DependencyContainerInterface $dependencyManager, Rule $rulePrototype)
    {
        $this->dependencyManager = $dependencyManager;
        $this->rulePrototype     = $rulePrototype;
    }

    /**
     * @param string $controller Class name or alias
     *
     * @return $this
     */
    public function catchall($controller)
    {
        $this->catchallController = $controller;

        return $this;
    }

    /**
     * Define a routing rule with corresponding controller
     *
     * @param string $path
     * @param string $controller Class name or alias
     * @param array  $expressions
     *
     * @return $this
     */
    public function define($path, $controller, $expressions = array())
    {
        $rule = clone $this->rulePrototype;

        $rule
            ->setPath($path)
            ->setController($controller)
            ->setExpressions($expressions);

        array_push($this->rules, $rule);

        return $this;
    }

    /**
     * @return array
     */
    public function getMatchParams()
    {
        return $this->matchParams;
    }

    /**
     * @param string $url
     *
     * @return object
     */
    public function match($url)
    {
        $decoded = urldecode($url);

        usort($this->rules, array($this, 'sort'));

        foreach ($this->rules as $rule) {
            $match = $rule->match($decoded);

            if ($match === false) {
                continue;
            }

            $this->matchParams = $match;

            return $this->dependencyManager->get($rule->getController());
        }



        return $this->dependencyManager->get($this->catchallController);
    }

    /**
     * @param Rule $a
     * @param Rule $b
     *
     * @return int
     */
    protected function sort($a, $b)
    {
        $complexityA = $a->complexity();
        $complexityB = $b->complexity();

        if ($complexityA === $complexityB) {
            return 0;
        }

        return $complexityA > $complexityB ? -1 : 1;
    }
}
