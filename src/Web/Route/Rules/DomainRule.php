<?php

namespace Web\Route\Rules;

use RuntimeException;
use Web\Route\Abstraction\AbstractRule;

class DomainRule extends AbstractRule
{
    /**
     * Count the number of pattern segments for this rule.
     */
    public function complexity()
    {
        return substr_count($this->pattern, '.');
    }

    /**
     * @return string
     */
    protected function resolveExpressionFromPattern()
    {
        $parts = explode('.', $this->pattern);

        foreach ($parts as &$part) {
            $match = array();

            if (preg_match('/^(\:|\?)([a-z\d]{1}[a-z\d\-]*)$/i', $part, $match)) {
                $optional            = $match[1] === '?' ? '?' : '';
                $name                = $match[2];
                $this->params[$name] = '';
                $part                = '([^\.]+)' . $optional;
                $this->captureKeys[] = $name;
            }
            else {
                $part = preg_quote($part, '/');
            }
        }

        return '/^' . implode('\.', $parts) . '(?:\.([^\.]+))*$/i';
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function clean($value)
    {
        return filter_var($value, FILTER_SANITIZE_STRING);
    }

    /**
     * @param string $value
     *
     * @return string
     * @throws RuntimeException
     */
    public function validate($value)
    {
        if (empty($value)) {
            throw new RuntimeException('Expected a domain value');
        }

        if (strlen($value) > 253) {
            throw new RuntimeException("Specified domain '$value' is too long. Maximum length of 253 characters permitted");
        }

        if (!preg_match('/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i', $value)) {
            throw new RuntimeException("Specified domain '$value' contains illegal characters.");
        }

        if (!preg_match('/^[^\.]{1,63}(\.[^\.]{1,63})*$/', $value)) {
            throw new RuntimeException("Specified domain '$value' contains a segment that is too long. Maximum segment length of 63 characters permitted.");
        }

        return $value;
    }
} 
