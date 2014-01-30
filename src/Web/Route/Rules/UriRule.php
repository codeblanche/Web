<?php

namespace Web\Route\Rules;

use Web\Route\Abstraction\AbstractRule;

class UriRule extends AbstractRule
{
    /**
     * Count the number of pattern segments for this rule.
     */
    public function complexity()
    {
        $dynamicPartsCount = substr_count($this->pattern, ':') + substr_count($this->pattern, '?');

        return (substr_count($this->pattern, '/') - $dynamicPartsCount) * 100 + $dynamicPartsCount;
    }

    /**
     * @return string
     */
    protected function resolveExpressionFromPattern()
    {
        $parts = explode('/', $this->pattern);

        foreach ($parts as &$part) {
            $match = array();

            if (preg_match('/^(\:|\?)([a-z_]{1}[a-z\d\-_]*)$/i', $part, $match)) {
                $optional            = $match[1] === '?' ? '?' : '';
                $name                = $match[2];
                $this->params[$name] = '';
                $part                = $optional . '([^\/\?]+)' . $optional;
                $this->captureKeys[] = $name;
            }
            else {
                $part = preg_quote($part, '/');
            }
        }

        return '/^' . implode('\/', $parts) . '(?:\/([^\/\?]+))*(?:\?.+)?$/i';
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function clean($value)
    {
        return '/' . ltrim(filter_var(urldecode($value), FILTER_SANITIZE_STRING), '/');
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function validate($value)
    {
        return $value;
    }
}
