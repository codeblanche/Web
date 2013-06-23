<?php

namespace Web\Request;

class Request
{
    /**
     * HTTP method constants.
     */
    const METHOD_DELETE = 'DELETE';

    const METHOD_GET = 'GET';

    const METHOD_HEAD = 'HEAD';

    const METHOD_OPTIONS = 'OPTIONS';

    const METHOD_POST = 'POST';

    const METHOD_PUT = 'PUT';

    const METHOD_TRACE = 'TRACE';

    /**
     * WebDAV method constants.
     */
    const METHOD_COPY = 'COPY';

    const METHOD_LOCK = 'LOCK';

    const METHOD_MKCOL = 'MKCOL';

    const METHOD_MOVE = 'MOVE';

    const METHOD_PROPFIND = 'PROPFIND';

    const METHOD_PROPPATCH = 'PROPPATCH';

    const METHOD_UNLOCK = 'UNLOCK';

    /**
     * Auth constants
     */
    const AUTH_BASIC = 'BASIC';

    const AUTH_DIGEST = 'DIGEST';

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $query;

    /**
     * @var string
     */
    protected $dirname;

    /**
     * @var string
     */
    protected $basename;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var string
     */
    protected $filename;

    /**
     * Default constructor
     *
     * @param string $requestUri
     */
    public function __construct($requestUri = null)
    {
        $this->uri = $this->resolveUri($requestUri);

        $this->parseUri();
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

    /**
     * @param string $name
     *
     * @return string
     */
    public function server($name)
    {
        return filter_input(INPUT_SERVER, $name);
    }

    protected function parseUri()
    {
        if (empty($this->uri)) {
            return;
        }

        $parts = parse_url($this->uri);

        if (isset($parts['path'])) {
            $this->path = $parts['path'];

            $this->parsePath();
        }

        if (isset($parts['query'])) {
            $this->query = $parts['query'];
        }
    }

    protected function parsePath()
    {
        if (empty($this->path)) {
            return;
        }

        $parts = pathinfo($this->path);

        if (isset($parts['dirname'])) {
            $this->dirname = $parts['dirname'];
        }

        if (isset($parts['basename'])) {
            $this->basename = $parts['basename'];
        }

        if (isset($parts['extension'])) {
            $this->extension = $parts['extension'];
        }

        if (isset($parts['filename'])) {
            $this->filename = $parts['filename'];
        }
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
     * @param string $destinationDir
     * @param string $newName
     *
     * @return bool
     */
    public function file($name, $destinationDir, $newName = '')
    {
        $destinationName = $this->resolveName($newName);

        if ($this->server('REQUEST_METHOD') === self::METHOD_PUT) {
            $destination = fopen("$destinationDir/$destinationName", "w+");
            $source      = fopen("php://input", "r");

            if (!is_resource($destination) || !is_resource($source)) {
                return false;
            }

            while ($buffer = fread($source, 1024)) {
                fwrite($destination, $buffer, 1024);
            }

            fclose($destination);
            fclose($source);
        }
        else {
            if (!isset($_FILES[$name])) {
                return false;
            }

            move_uploaded_file($_FILES[$name]['tmp_name'], "$destinationDir/$destinationName");
        }

        return true;
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
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
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
     * @return mixed
     */
    public function env($name)
    {
        return filter_input(INPUT_ENV, $name);
    }

    /**
     * @return string
     */
    public function getBasename()
    {
        return $this->basename;
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return $this->dirname;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getDocumentRoot()
    {
        return $this->server('DOCUMENT_ROOT');
    }
}
