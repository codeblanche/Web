<?php

namespace Web;

/**
 * Assists in the extractions data from a query string
 */
class QueryString
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * Constructor override.
     *
     * @param array|string $input
     */
    public function __construct($input = null)
    {
        if (empty($input)) {
            return;
        }

        $this->import($input);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Remove all query string values
     *
     * @return QueryString
     */
    public function clear()
    {
        $this->data = array();

        return $this;
    }

    /**
     * Retrieve a query string value (by reference)
     *
     * @param string $key
     * @param bool   $sanitize
     * @param bool   $array
     *
     * @return mixed
     */
    public function get($key, $sanitize = true, $array = false)
    {

        if (!isset($this->data[$key])) {
            return $array ? array() : null;
        }

        $filter = 0;
        $flags  = FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE;

        if ($sanitize) {
            $filter = $filter | FILTER_SANITIZE_STRING;
            $flags  = $flags | FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH;
        }
        if ($array) {
            $flags = $flags | FILTER_REQUIRE_ARRAY | FILTER_FORCE_ARRAY;
        }

        return filter_var($this->data[$key], $filter, array('flags' => $flags));
    }

    /**
     * Test if the given key is defined
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Import a query string or array of data
     *
     * @param array|string $input
     *
     * @return QueryString
     */
    public function import($input)
    {
        if (is_array($input)) {
            $this->fromArray($input);
        }
        else {
            $this->fromString($input);
        }

        return $this;
    }

    /**
     * Unset a query string value
     *
     * @param string $key
     *
     * @return QueryString
     */
    public function remove($key)
    {
        unset($this->data[$key]);

        return $this;
    }

    /**
     * Set a query string value
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return QueryString
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Extract data to an array
     *
     * @param boolean $ignoreEmpty
     *
     * @return array
     */
    public function toArray($ignoreEmpty = false)
    {
        if (!$ignoreEmpty) {
            return $this->data;
        }

        return array_filter(
            $this->data,
            function ($value) {
                return !empty($value);
            }
        );
    }

    /**
     * Extract data as a query string.
     *
     * @param boolean $ignoreEmpty Omit empty values.
     * @param string  $numPrefix   Prefix numeric keys
     * @param string  $separator   Query string value separator.
     *
     * @return string
     */
    public function toString($ignoreEmpty = false, $numPrefix = null, $separator = null)
    {
        if (empty($separator)) {
            $separator = '&';
        }

        return http_build_query(
            $this->toArray($ignoreEmpty),
            $numPrefix,
            $separator
        );
    }

    /**
     * Test if the given key is empty
     *
     * @param string $key
     *
     * @return bool
     */
    public function vacant($key)
    {
        return empty($this->data[$key]);
    }

    /**
     * Set data from an array
     *
     * @param array $data
     *
     * @return QueryString
     */
    protected function fromArray($data)
    {
        if (empty($data)) {
            return $this;
        }

        $this->data = array_replace_recursive($this->data, $data);

        return $this;
    }

    /**
     * Set data from a query string
     *
     * @param string $queryString
     *
     * @return QueryString
     */
    protected function fromString($queryString)
    {
        if (empty($queryString)) {
            return $this;
        }

        $data = array();

        parse_str($queryString, $data);

        $this->fromArray($data);

        return $this;
    }
}

