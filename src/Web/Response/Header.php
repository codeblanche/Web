<?php

namespace Web\Response;

use Web\Response\Abstraction\HeaderInterface;

class Header implements HeaderInterface
{
    const ACCEPT_RANGES = 'Accept-Ranges';

    const ACCESS_CONTROL_ALLOW_ORIGIN = 'Access-Control-Allow-Origin';

    const AGE = 'Age';

    const ALLOW = 'Allow';

    const CACHE_CONTROL = 'Cache-Control';

    const CONNECTION = 'Connection';

    const CONTENT_ENCODING = 'Content-Encoding';

    const CONTENT_DISPOSITION = 'Content-Disposition';

    const CONTENT_LANGUAGE = 'Content-Language';

    const CONTENT_LENGTH = 'Content-Length';

    const CONTENT_LOCATION = 'Content-Location';

    const CONTENT_MD5 = 'Content-MD5';

    const CONTENT_RANGE = 'Content-Range';

    const CONTENT_TYPE = 'Content-Type';

    const DATE = 'Date';

    const ETAG = 'ETag';

    const EXPIRES = 'Expires';

    const LAST_MODIFIED = 'Last-Modified';

    const LINK = 'Link';

    const LOCATION = 'Location';

    const PRAGMA = 'Pragma';

    const PROXY_AUTHENTICATE = 'Proxy-Authenticate';

    const REFRESH = 'Refresh';

    const RETRY_AFTER = 'Retry-After';

    const SERVER = 'Server';

    const SET_COOKIE = 'Set-Cookie';

    const STATUS = 'Status';

    const STRICT_TRANSPORT_SECURITY = 'Strict-Transport-Security';

    const TRAILER = 'Trailer';

    const TRANSFER_ENCODING = 'Transfer-Encoding';

    const VARY = 'Vary';

    const VIA = 'Via';

    const WWW_AUTHENTICATE = 'WWW-Authenticate';

    const WARNING = 'Warning';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $name
     * @param string $value
     */
    function __construct($name, $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * Cast to string handler
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Retrieve string representation of Header
     *
     * @return string
     */
    public function toString()
    {
        return "$this->name: $this->value";
    }

    /**
     * @param string $name
     *
     * @return Header
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $value
     *
     * @return Header
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
