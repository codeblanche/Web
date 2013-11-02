<?php

namespace Web;

/**
 * Assists in the extractions data from a uri
 */
class Uri
{
    /**
     * @var string
     */
    protected $basename;

    /**
     * @var string
     */
    protected $dirname;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $fragment;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $pass;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var integer
     */
    protected $port;

    /**
     * @var QueryString
     */
    protected $query;

    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var string
     */
    protected $user;

    /**
     * Constructor override.
     *
     * @param string|array $input
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
     * @return string
     */
    public function getBasename()
    {
        return $this->basename;
    }

    /**
     * @return string
     */
    public function getDirname()
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
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return QueryString
     * @return Uri
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Import the given input
     *
     * @param string|array $input
     *
     * @return Uri
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
     * @param string $basename
     *
     * @return Uri
     */
    public function setBasename($basename)
    {
        $this->parsePath($basename)->buildPath();

        return $this;
    }

    /**
     * @param string $dirname
     *
     * @return Uri
     */
    public function setDirname($dirname)
    {
        $this->dirname = $dirname;

        $this->buildPath();

        return $this;
    }

    /**
     * @param string $extension
     *
     * @return Uri
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        $this->buildPath();

        return $this;
    }

    /**
     * @param string $filename
     *
     * @return Uri
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        $this->buildPath();

        return $this;
    }

    /**
     * @param string $fragment
     *
     * @return Uri
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;

        return $this;
    }

    /**
     * @param string $host
     *
     * @return Uri
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param string $pass
     *
     * @return Uri
     */
    public function setPass($pass)
    {
        $this->pass = $pass;

        return $this;
    }

    /**
     * @param string $path
     *
     * @return Uri
     */
    public function setPath($path)
    {
        $this->parsePath($path)->buildPath();

        return $this;
    }

    /**
     * @param int $port
     *
     * @return Uri
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @param QueryString|string $query
     *
     * @return Uri
     */
    public function setQuery($query)
    {
        if (!($query instanceof QueryString)) {
            $this->query = new QueryString($query);

            return $this;
        }

        $this->query = $query;

        return $this;
    }

    /**
     * @param string $scheme
     *
     * @return Uri
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * @param string $user
     *
     * @return Uri
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Convert to an array
     *
     * @return array
     */
    public function toArray()
    {
        $qs = $this->query instanceof QueryString
            ? $this->query->toString()
            : '';

        return array(
            'scheme'    => $this->scheme,
            'host'      => $this->host,
            'port'      => $this->port,
            'user'      => $this->user,
            'pass'      => $this->pass,
            'path'      => $this->path,
            'basename'  => $this->basename,
            'dirname'   => $this->dirname,
            'extension' => $this->extension,
            'filename'  => $this->filename,
            'query'     => $qs,
            'fragment'  => $this->fragment,
        );
    }

    /**
     * Convert to a string.
     *
     * @return string
     */
    public function toString()
    {
        $result = "";

        if (!empty($this->scheme)) {
            $result .= "{$this->scheme}://";
        }

        if (!empty($this->user)) {
            $result .= !empty($this->pass)
                ? "{$this->user}:{$this->pass}@"
                : "{$this->user}@";
        }

        $result .= $this->host;

        if (!empty($this->port)) {
            $result .= ":{$this->port}";
        }

        $result .= $this->path;

        $qs = $this->query->toString();
        if (!empty($qs)) {
            $result .= "?{$qs}";
        }

        if (!empty($this->fragment)) {
            $result .= "#{$this->fragment}";
        }

        return $result;
    }

    /**
     * Rebuild the path using the path components
     *
     * @return Uri
     */
    protected function buildPath()
    {
        $this->basename = $this->filename;

        if (!empty($this->extension)) {
            $this->basename .= '.' . $this->extension;
        }

        $this->path = $this->dirname;

        if (!empty($this->basename)) {
            $this->path .= '/' . $this->basename;
        }

        return $this;
    }

    /**
     * Import from an array
     *
     * @param array $array
     *
     * @return Uri
     */
    protected function fromArray($array)
    {
        if (empty($array)) {
            return $this;
        }

        $this->query = new QueryString();

        if (!empty($array['query'])) {
            $this->query->import($array['query']);
        }

        $keys = array(
            'scheme',
            'host',
            'port',
            'user',
            'pass',
            'path',
            'fragment',
            'dirname',
            'basename',
            'extension',
            'filename',
        );

        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                continue;
            }

            $this->$key = $array[$key];

            if ($key === 'path') {
                $this->parsePath($this->path);
            }
        }

        $this->buildPath();

        return $this;
    }

    /**
     * Import from a string
     *
     * @param string $uri
     *
     * @return Uri
     */
    protected function fromString($uri)
    {
        if (empty($uri)) {
            return $this;
        }

        $this->fromArray(parse_url($uri));

        return $this;
    }

    /**
     * Extract the path components from the current path
     *
     * @param string $path
     *
     * @return Uri
     */
    protected function parsePath($path = null)
    {
        $this->dirname   = '';
        $this->basename  = '';
        $this->extension = '';
        $this->filename  = '';

        if (empty($path)) {
            return $this;
        }

        $parts = pathinfo($path);

        if (isset($parts['dirname'])) {
            $this->dirname = rtrim($parts['dirname'], '/');
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

        return $this;
    }
}
