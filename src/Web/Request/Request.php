<?php

namespace Web\Request;

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
     */
    public function __construct($requestUri = null)
    {
        $this->uri = new Uri(
            $this->resolveUri($requestUri)
        );
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
        return filter_input(INPUT_COOKIE, $name);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function env($name)
    {
        return filter_input(INPUT_ENV, $name);
    }

    /**
     * Move an uploaded file (POST method) to the desired destination
     *
     * @param string $name
     * @param string $destinationDir
     * @param string $newName
     *
     * @return bool
     */
    public function filePost($name, $destinationDir, $newName = '')
    {
        if (!isset($_FILES[$name])) {
            return false;
        }

        $destinationName = $_FILES[$name]['name'];

        if (!empty($newName)) {
            $destinationName = $newName;
        }

        return move_uploaded_file($_FILES[$name]['tmp_name'], "$destinationDir/$destinationName");
    }

    /**
     * Move an uploaded file (PUT method) to the desired destination
     *
     * @param string $destinationDir
     * @param string $newName
     *
     * @return bool
     */
    public function filePut($destinationDir, $newName = '')
    {
        $destinationName = $this->resolveName($newName);
        $destination     = fopen("$destinationDir/$destinationName", "w+");
        $source          = fopen("php://input", "r");

        if (!is_resource($destination) || !is_resource($source)) {
            return false;
        }

        while ($buffer = fread($source, 1024)) {
            fwrite($destination, $buffer, 1024);
        }

        fclose($destination);
        fclose($source);

        return true;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        return filter_input(INPUT_POST, $name);
    }

    /**
     * @return string
     */
    public function getBasename()
    {
        return $this->uri->getBasename();
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return $this->uri->getDirname();
    }

    /**
     * @return string
     */
    public function getDocumentRoot()
    {
        return $this->server('DOCUMENT_ROOT');
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->uri->getExtension();
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->uri->getFilename();
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->uri->getPath();
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->uri->getQuery()->toString();
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri->toString();
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function post($name)
    {
        return filter_input(INPUT_POST, $name);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function server($name)
    {
        return filter_input(INPUT_SERVER, $name);
    }

    /**
     * @param $override
     *
     * @return string
     */
    protected function resolveName($override)
    {
        if (!empty($override)) {
            return $override;
        }

        return $this->getFilename();
    }

    /**
     * @param $uri
     *
     * @return string
     */
    protected function resolveUri($uri)
    {
        if (empty($uri)) {
            return $this->server('REQUEST_URI');
        }

        return $uri;
    }
}
