<?php

namespace Web\Route;

use Web\Exception\RuntimeException;
use Web\Route\Abstraction\AbstractRule;
use Web\Route\Abstraction\DependencyContainerInterface;

class Router
{
    const ROUTE_TYPE_URI = 'uri';

    const ROUTE_TYPE_DOMAIN = 'domain';

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
     * @var AbstractRule
     */
    protected $rulePrototype;

    /**
     * @var string
     */
    protected $routeType;

    /**
     * @var AbstractRule[]
     */
    protected $rules = array();

    /**
     * @var array
     */
    protected $routeTypeRulePrototypeMap = array(
        'uri'    => 'Web\Route\Rules\UriRule',
        'domain' => 'Web\Route\Rules\DomainRule',
    );

    /**
     * @param DependencyContainerInterface $dependencyManager
     * @param string                       $routeType
     */
    public function __construct(DependencyContainerInterface $dependencyManager, $routeType = self::ROUTE_TYPE_URI)
    {
        $this->dependencyManager = $dependencyManager;
        $this->routeType         = $routeType;

        $this->resolveRulePrototype();
    }

    /**
     * Resolve the rule prototype object for the current routeType
     *
     * @throws \Web\Exception\RuntimeException
     */
    private function resolveRulePrototype()
    {
        if (!isset($this->routeTypeRulePrototypeMap[$this->routeType])) {
            throw new RuntimeException("Unable to resolve rule for route type '$this->routeType'. Route type is not supported.");
        }

        $prototypeClass = $this->routeTypeRulePrototypeMap[$this->routeType];
        $prototype      = $this->dependencyManager->get($prototypeClass);

        if (!($prototype instanceof AbstractRule)) {
            $givenType = get_class($prototype);

            throw new RuntimeException("The resolved rule prototype must inherit from Web\Route\Rule. Rule of type '$givenType' is not valid.");
        }

        $this->rulePrototype = $prototype;
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
     * @param string $pattern
     * @param string $controller Class name or alias
     * @param array  $filters
     *
     * @return $this
     */
    public function define($pattern, $controller, $filters = array())
    {
        $rule = clone $this->rulePrototype;

        $rule->setPattern($pattern)->setController($controller)->setFilters($filters);

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
     * @param string $value
     *
     * @return object
     */
    public function match($value)
    {
        usort($this->rules, array($this, 'sort'));

        foreach ($this->rules as $rule) {
            $match = $rule->match($value);

            if ($match === false) {
                continue;
            }

            $this->matchParams = $match;

            return $this->dependencyManager->get($rule->getController());
        }

        return $this->dependencyManager->get($this->catchallController);
    }

    /**
     * @param AbstractRule $a
     * @param AbstractRule $b
     *
     * @return int
     */
    protected function sort($a, $b)
    {
        $complexityA = $a->complexity();
        $complexityB = $b->complexity();

        return $complexityB - $complexityA;
    }

    /**
     * @param string $routeType
     *
     * @return Router
     */
    public function setRouteType($routeType)
    {
        $this->routeType = $routeType;

        $this->resolveRulePrototype();

        return $this;
    }
}
