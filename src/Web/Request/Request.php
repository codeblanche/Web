<?php

namespace Web\Request;

use InvalidArgumentException;
use Web\Exception\RuntimeException;
use Web\Uri;

class Request
{
    /**
     * @var Uri
     */
    protected $uri;

    /**
     * @var array Order in which to check request collections for a value. [G = GET, P = POST, C = COOKIE, S = SESSION, E = ENV, H = SERVER]
     */
    protected $requestOrder = array('G', 'P');

    /**
     * @var array Request order method mapping
     */
    protected $requestOrderMap = array(
        'G' => 'get',
        'P' => 'post',
        'C' => 'cookie',
        'S' => 'session',
        'E' => 'env',
        'H' => 'server',
    );

    /**
     * Default constructor
     *
     * @param string $requestUri
     * @param string $requestOrder
     *
     * @return \Web\Request\Request
     */
    public function __construct($requestUri = null, $requestOrder = 'GP')
    {
        if (empty($requestUri)) {
            $requestUri = $this->server('REQUEST_URI');
        }

        $this->uri = new Uri($requestUri);

        $this->setRequestOrder($requestOrder);
    }

    /**
     * Set the request order.
     *
     * @param string $requestOrder The order in which to check request collections for a value. [G = GET, P = POST, C = COOKIE, S = SESSION, E = ENV, H = SERVER]
     *
     * @throws InvalidArgumentException
     */
    public function setRequestOrder($requestOrder)
    {
        if (!is_string($requestOrder)) {
            throw new InvalidArgumentException('Expected requestOrder to be a string');
        }

        if (empty($requestOrder)) {
            throw new InvalidArgumentException('An empty string is not a valid requestOrder value');
        }

        $this->requestOrder = array_unique(str_split(strtoupper($requestOrder), 1));
    }


    /**
     * Retrieve a value using the configured request order
     *
     * @param string $name
     *
     * @return mixed
     * @throws RuntimeException
     */
    public function value($name)
    {
        if (empty($this->requestOrder)) {
            throw new RuntimeException('Unable to determine the request order');
        }

        $result = null;

        foreach ($this->requestOrder as $token) {
            $method = $this->resolveRequestOrderMethod($token);

            if (empty($method)) {
                continue;
            }

            $result = $this->$method($name);

            if (!is_null($result)) {
                break;
            }
        }

        return $result;
    }

    /**
     * @param $token
     *
     * @return string
     * @throws RuntimeException
     */
    protected function resolveRequestOrderMethod($token)
    {
        if (empty($this->requestOrderMap[$token])) {
            return '';
        }

        $method = $this->requestOrderMap[$token];

        if (!method_exists($this, $method)) {
            throw new RuntimeException('Defined request order map method does not exist.');
        }

        return $method;
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

    /**
     * Retrieve the current domain
     *
     * @param int $maxLevels Trim the domain to max levels
     *
     * @return string
     */
    public function domain($maxLevels = 0)
    {
        $parts = explode('.', $this->uri()->getHost());

        return implode('.', array_slice($parts, -1 * $maxLevels));
    }
}
