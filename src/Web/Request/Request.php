<?php

namespace Web\Request;

use InvalidArgumentException;
use Web\Uri;

class Request
{
    /**
     * @var Uri
     */
    protected $uri;

    /**
     * Default constructor
     *
     * @param null $requestUri
     *
     * @return \Web\Request\Request
     */
    public function __construct($requestUri = null)
    {
        if (empty($requestUri)) {
            $requestUri = $this->server('REQUEST_URI');
        }

        $this->uri = new Uri($requestUri);
    }

    /**
     * Retrieve a value using the following pecking order POST, GET, COOKIE, SERVER, ENV
     *
     * @param string $name
     *
     * @return mixed
     */
    public function value($name)
    {
        $result = $this->post($name);

        if (is_null($result)) {
            $result = $this->get($name);
        }

        if (is_null($result)) {
            $result = $this->cookie($name);
        }

        if (is_null($result)) {
            $result = $this->server($name);
        }

        if (is_null($result)) {
            $result = $this->env($name);
        }

        return $result;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function cookie($name)
    {
        if (!isset($_COOKIE[$name])) {
            return null;
        }

        return $_COOKIE[$name];
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function env($name)
    {
        if (!isset($_ENV[$name])) {
            return null;
        }

        return $_ENV[$name];
    }

    /**
     * Retrieve the the date associated with a file upload
     *
     * @param string $name
     *
     * @return array
     */
    public function files($name)
    {
        if (!isset($_FILES[$name])) {
            return null;
        }

        return $_FILES[$name];
    }

    /**
     * Retrieve date from the input stream
     *
     * @param string $source
     *
     * @throws InvalidArgumentException
     * @return mixed
     */
    public function put($source = 'php://input')
    {
        $source = @fopen($source, 'r');

        if (!is_resource($source)) {
            throw new InvalidArgumentException('Expected parameter 1 to be an open-able resource');
        }

        $data = null;

        while ($buffer = fread($source, 1024)) {
            $data .= $buffer;
        }

        fclose($source);

        return $data;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        if (!isset($_GET[$name])) {
            return null;
        }

        return $_GET[$name];
    }

    /**
     * @return \Web\Uri
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function post($name)
    {
        if (!isset($_POST[$name])) {
            return null;
        }

        return $_POST[$name];
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function server($name)
    {
        if (!isset($_SERVER[$name])) {
            return null;
        }

        return $_SERVER[$name];
    }
}
