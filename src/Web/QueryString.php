<?php

/**
 * Assists in the extractions of parts from a uri.
 *
 * @author merten
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
     * Set data from an array
     *
     * @param   array $data
     *
     * @return  QueryString
     */
    protected function fromArray($data)
    {
        if (empty($data)) {
            return $this;
        }

        $this->fromArray(array_replace_recursive($this->data, $data));

        return $this;
    }

    /**
     * Set data from a query string
     *
     * @param   string $queryString
     *
     * @return  QueryString
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

    /**
     * Retrieve a query string value (by reference)
     *
     * @param   string $key
     *
     * @return  mixed
     */
    public function &get($key)
    {
        if (!isset($this->data[$key])) {
            return null;
        }

        return $this->data[$key];
    }

    /**
     * @param array|string $input
     */
    public function import($input)
    {
        if (is_array($input)) {
            $this->fromArray($input);
        }
        else {
            $this->fromString($input);
        }
    }

    /**
     * Unset a query string value
     *
     * @param   string $key
     *
     * @return  QueryString
     */
    public function remove($key)
    {
        unset($this->data[$key]);

        return $this;
    }

    /**
     * Remove all query string values
     *
     * @return  QueryString
     */
    public function clear()
    {
        $this->data = array();

        return $this;
    }

    /**
     * Set a query string value
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  QueryString
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Extract data to an array
     *
     * @param   boolean $ignoreEmpty
     *
     * @return  array
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
     * @param   boolean $ignoreEmpty    Omit empty values.
     * @param   string  $numPrefix      Prefix numeric keys
     * @param   string  $separator      Query string value separator.
     *
     * @return  string
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
}

