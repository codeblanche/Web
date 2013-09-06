<?php

namespace Web\Response;

use Web\Response\Abstraction\HeaderInterface;
use Web\Response\Abstraction\StatusInterface;

class Status implements StatusInterface, HeaderInterface
{
    const ACCEPTED = 202;

    const BAD_GATEWAY = 502;

    const BAD_REQUEST = 400;

    const CONFLICT = 409;

    const CONTINUE_ = 100;

    const CREATED = 201;

    const EXPECTATION_FAILED = 417;

    const FORBIDDEN = 403;

    const FOUND = 302;

    const GATEWAY_TIMEOUT = 504;

    const GONE = 410;

    const HTTP_VERSION_NOT_SUPPORTED = 505;

    const INTERNAL_SERVER_ERROR = 500;

    const LENGTH_REQUIRED = 411;

    const METHOD_NOT_ALLOWED = 405;

    const MOVED_PERMANENTLY = 301;

    const MULTIPLE_CHOICES = 300;

    const NON_AUTHORITATIVE_INFORMATION = 203;

    const NOT_ACCEPTABLE = 406;

    const NOT_FOUND = 404;

    const NOT_IMPLEMENTED = 501;

    const NOT_MODIFIED = 304;

    const NO_CONTENT = 204;

    const OK = 200;

    const PARTIAL_CONTENT = 206;

    const PAYMENT_REQUIRED = 402;

    const PRECONDITION_FAILED = 412;

    const PROXY_AUTHENTICATION_REQUIRED = 407;

    const REQUESTED_RANGE_NOT_SATISFIABLE = 416;

    const REQUEST_ENTITY_TOO_LARGE = 413;

    const REQUEST_TIMEOUT = 408;

    const REQUEST_URI_TOO_LONG = 414;

    const RESET_CONTENT = 205;

    const SEE_OTHER = 303;

    const SERVICE_UNAVAILABLE = 503;

    const SWITCHING_PROTOCOLS = 101;

    const TEMPORARY_REDIRECT = 307;

    const UNAUTHORIZED = 401;

    const UNSUPPORTED_MEDIA_TYPE = 415;

    const USE_PROXY = 305;

    /**
     * List of default HTTP status messages.
     *
     * @var array
     */
    protected $defaultStatusTextList = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    );

    /**
     * The HTTP status code of the message.
     *
     * @var int
     */
    protected $statusCode = self::OK;

    /**
     * Default constructor
     *
     * @param $statusCode
     */
    function __construct($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * Retrieve the status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Retrieve the status text or empty string if one cannot be found
     *
     * @return string
     */
    public function getStatusText()
    {
        $statusCode = $this->getStatusCode();

        if (!isset($this->defaultStatusTextList[$statusCode])) {
            return '';
        }

        return $this->defaultStatusTextList[$statusCode];
    }

    /**
     * Retrieve string representation of Status
     *
     * @return string
     */
    public function toString()
    {
        $code = $this->getStatusCode();
        $text = $this->getStatusText();

        return "HTTP/1.1 $code $text";
    }

    /**
     * Cast to string handler
     *
     * return string
     */
    function __toString()
    {
        return $this->toString();
    }
}
