<?php

namespace Web\Request;

use InvalidArgumentException;
use Web\Exception\RuntimeException;
use Web\QueryString;
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
    protected $requestOrder = array();

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
    public function __construct($requestUri = null, $requestOrder = 'GPC')
    {
        if (empty($requestUri)) {
            $requestUri = $this->resolveFullUrlFromHeaders();
        }

        $this->uri = new Uri($requestUri);

        $this->setRequestOrder($requestOrder);
    }

    /**
     * @return string
     */
    private function resolveFullUrlFromHeaders()
    {
        $rawProtocol = $this->server('SERVER_PROTOCOL');
        $ssl         = $this->server('HTTPS') !== '';

        $protocol = strtolower(substr($rawProtocol, 0, strpos($rawProtocol, '/'))) . ($ssl ? 's' : '');
        $host     = $this->server('HTTP_HOST');
        $port     = $this->server('SERVER_PORT');
        $path     = $this->server('REQUEST_URI');

        return "{$protocol}://{$host}:{$port}{$path}";
    }

    /**
     * Resolve a value from the given collection.
     *
     * @param string $name
     * @param array  $source
     * @param bool   $sanitize
     * @param bool   $array
     *
     * @return mixed
     */
    protected function resolveValue($name, &$source = null, $sanitize = true, $array = false)
    {
        $result = $array ? array() : null;

        if (empty($name)) {
            $result = $source;
        }
        elseif (isset($source[$name])) {
            $result = $source[$name];
        }

        if (!$sanitize) {
            return $result;
        }

        if (is_array($result)) {
            foreach ($result as $key => $value) {
                $result[$key] = $this->resolveValue('', $value, $sanitize);
            }
        }
        else {
            $filter = 0;
            $flags  = FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE;

            if ($sanitize) {
                $filter = $filter | FILTER_SANITIZE_STRING;
                $flags  = $flags | FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH;
            }

            if ($array) {
                $flags = $flags | FILTER_FORCE_ARRAY;
            }

            $result = filter_var($result, $filter, array('flags' => $flags));
        }

        return $result;
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
     * @param bool   $sanitize
     *
     * @return mixed
     */
    public function cookie($name, $sanitize = true)
    {
        return $this->resolveValue($name, $_COOKIE, $sanitize);
    }

    /**
     * @param string $name
     * @param bool   $sanitize
     *
     * @return mixed
     */
    public function env($name, $sanitize = true)
    {
        return $this->resolveValue($name, $_ENV, $sanitize);
    }

    /**
     * Retrieve the the date associated with a file upload
     *
     * @param string $name
     * @param bool   $sanitize
     *
     * @return array
     */
    public function files($name, $sanitize = true)
    {
        return $this->resolveValue($name, $_FILES, $sanitize, true);
    }

    /**
     * Retrieve date from the input stream
     *
     * @param string $source (Default: 'php://input')
     * @param bool   $sanitize
     *
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function put($source = 'php://input', $sanitize = true)
    {
        if (is_null($source)) {
            $source = 'php://input';
        }

        $source = @fopen($source, 'r');

        if (!is_resource($source)) {
            throw new InvalidArgumentException('Expected parameter 1 to be an open-able resource');
        }

        $data = null;

        while ($buffer = fread($source, 1024)) {
            $data .= $buffer;
        }

        fclose($source);

        return $sanitize ? filter_var($data, FILTER_SANITIZE_STRING) : $data;
    }

    /**
     * @param string $name
     * @param bool   $sanitize
     *
     * @return mixed
     */
    public function get($name = '', $sanitize = true)
    {
        $queryString = $this->uri()->getQuery();

        if ($queryString instanceof QueryString && $queryString->has($name)) {
            return $queryString->get($name);
        }

        return $this->resolveValue($name, $_GET, $sanitize);
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
     * @param bool   $sanitize
     *
     * @return mixed
     */
    public function post($name = '', $sanitize = true)
    {
        return $this->resolveValue($name, $_POST, $sanitize);
    }

    /**
     * @param string $name
     * @param bool   $sanitize
     *
     * @return string
     */
    public function server($name = '', $sanitize = true)
    {
        return $this->resolveValue($name, $_SERVER, $sanitize);
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

    /**
     * Retrieve the requests protocol
     *
     * @param bool $raw Also return protocol version if available
     *
     * @return string
     */
    public function protocol($raw = false)
    {
        if ($raw) {
            return $this->server('SERVER_PROTOCOL');
        }

        $parts = explode('/', $this->server('SERVER_PROTOCOL'));

        return strtolower(array_shift($parts)) . $this->server('HTTPS') === 'on' ? 's' : '';
    }
}
