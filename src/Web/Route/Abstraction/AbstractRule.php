<?php

namespace Web\Route\Abstraction;

abstract class AbstractRule
{
    /**
     * @var array
     */
    protected $captureKeys = array();

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string[]
     */
    protected $filters = array();

    /**
     * @var array Key=>Value pairs
     */
    protected $params = array();

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var string
     */
    protected $expression;

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $value
     *
     * @return array|bool
     */
    public function match($value)
    {
        $match = array();
        $clean = $this->validate($this->clean($value));

        if (!preg_match($this->expression, $clean, $match)) {
            return false;
        }

        $params = array_slice($match, 1);

        for ($i = count($this->captureKeys) - 1; $i >= 0; $i--) {
            if (!isset($params[$i])) {
                continue;
            }

            $key                = $this->captureKeys[$i];
            $this->params[$key] = $params[$i];

            unset($params[$i]);
        }

        $this->params = array_merge($this->params, $params);

        ksort($this->params, SORT_STRING);

        if (!$this->matchFilters($this->params)) {
            return false;
        }

        return $this->params;
    }

    /**
     * @param string $controller
     *
     * @return $this
     */
    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * @param string[] $expressions
     *
     * @return $this
     */
    public function setFilters($expressions)
    {
        $this->filters = $expressions;

        return $this;
    }

    /** @param string $pattern
     * @return $this
     */
    public function setPattern($pattern)
    {
        $this->pattern    = $pattern;
        $this->expression = $this->convertToExpression($pattern);

        return $this;
    }

    /**
     * Validate params against given expressions.
     *
     * @param $params
     *
     * @return bool
     */
    protected function matchFilters($params)
    {
        foreach ($this->filters as $key => $expression) {
            if (!isset($params[$key])) {
                return false;
            }

            if (!preg_match($expression, $params[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Count the number of pattern segments for this rule.
     */
    abstract public function complexity();

    /**
     * @return string
     */
    abstract protected function resolveExpressionFromPattern();

    /**
     * @param string $value
     *
     * @return string
     */
    abstract public function clean($value);

    /**
     * @param string $value
     *
     * @return string
     */
    abstract public function validate($value);
}
