<?php

namespace Web\Response;

class Cookie
{
    protected $domain;

    protected $expires = 0;

    protected $httpOnly = false;

    protected $name;

    protected $path;

    protected $secure = false;

    protected $value;

    /**
     * Default constructor
     *
     * @param string $name
     * @param string $value
     * @param int|string $expires
     */
    public function __construct($name, $value, $expires)
    {
        $this
            ->setName($name)
            ->setValue($value)
            ->setExpires($expires);
    }

    /**
     * Send the cookie
     */
    public function commit()
    {
        return setcookie(
            $this->name,
            $this->value,
            $this->expires,
            $this->path,
            $this->domain,
            $this->secure,
            $this->httpOnly
        );
    }

    /**
     * @param mixed $domain
     *
     * @return Cookie
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param int|string $expires
     *
     * @return Cookie
     */
    public function setExpires($expires)
    {
        if (empty($expires)) {
            return $this;
        }

        $this->expires = strtotime($expires);

        return $this;
    }

    /**
     * @return int
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * @param boolean $httpOnly
     *
     * @return Cookie
     */
    public function setIsHttpOnly($httpOnly)
    {
        $this->httpOnly = $httpOnly;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * @param string $name
     *
     * @return Cookie
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
     * @param mixed $path
     *
     * @return Cookie
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param boolean $secure
     *
     * @return Cookie
     */
    public function setIsSecure($secure)
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * @param string $value
     *
     * @return Cookie
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
