<?php

namespace Web\Route;

class Rule
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
    protected $expressions = array();

    /**
     * @var array Key=>Value pairs
     */
    protected $params = array();

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $pattern;

    /**
     * Count the number of pattern segments for this rule.
     */
    public function complexity()
    {
        return substr_count($this->path, '/');
    }

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
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $url
     *
     * @return array|bool
     */
    public function match($url)
    {
        $match = array();

        if (!preg_match($this->pattern, $url, $match)) {
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

        if (!$this->matchExpressions($this->params)) {
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
    public function setExpressions($expressions)
    {
        $this->expressions = $expressions;

        return $this;
    }

    /** @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path    = $path;
        $this->pattern = $this->convertToPattern($path);

        return $this;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function convertToPattern($path)
    {
        $parts = explode('/', $path);

        foreach ($parts as &$part) {
            $match = array();

            if (preg_match('/^(\:|\?)([a-z_]{1}[a-z0-9\-_]*)$/i', $part, $match)) {
                $optional            = $match[1] === '?' ? '?' : '';
                $name                = $match[2];
                $this->params[$name] = '';
                $part                = '([^\/\?]+)' . $optional;
                $this->captureKeys[] = $name;
            }
            else {
                $part = preg_quote($part, '/');
            }
        }

        return '/^' . implode('\/', $parts) . '(?:\/([^\/\?]+))*(?:\?.+)?$/i';
    }

    /**
     * Validate params against given expressions.
     *
     * @param $params
     *
     * @return bool
     */
    protected function matchExpressions($params)
    {
        foreach ($this->expressions as $key => $expression) {
            if (!isset($params[$key])) {
                return false;
            }

            if (!preg_match($expression, $params[$key])) {
                return false;
            }
        }

        return true;
    }
}
