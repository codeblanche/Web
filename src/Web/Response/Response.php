<?php

namespace Web\Response;

use RuntimeError;
use RuntimeException;
use Web\Response\Abstraction\HeaderInterface;
use Web\Response\Output\Abstraction\OutputStrategyInterface;
use Web\Response\Output\OutputHTML;

class Response
{
    /**
     * @var OutputStrategyInterface
     */
    protected $defaultOutputStrategy;

    /**
     * @param OutputStrategyInterface $defaultOutputStrategy
     */
    function __construct($defaultOutputStrategy = null)
    {
        if (!($defaultOutputStrategy instanceof OutputStrategyInterface)) {
            $defaultOutputStrategy = new OutputHTML();
        }

        $this->defaultOutputStrategy = $defaultOutputStrategy;
    }

    /**
     * @param string         $name
     * @param string         $value
     * @param integer|string $expires
     * @param string         $path
     * @param string         $domain
     * @param bool           $secure
     * @param bool           $httpOnly
     *
     * @return bool
     */
    public function cookie($name, $value, $expires = 0, $path = '', $domain = '', $secure = false, $httpOnly = false)
    {
        $cookie = new Cookie(
            $name,
            $value,
            $expires
        );

        return $cookie
            ->setPath($path)
            ->setDomain($domain)
            ->setIsSecure($secure)
            ->setIsHttpOnly($httpOnly)
            ->commit();
    }

    /**
     * @return OutputStrategyInterface
     */
    public function getDefaultOutputStrategy()
    {
        return $this->defaultOutputStrategy;
    }

    /**
     * @param HeaderInterface|string $header
     *
     * @return Response
     */
    public function header($header)
    {
        if ($header instanceof HeaderInterface) {
            $header = $header->toString();
        }

        if (empty($header)) {
            return $this;
        }

        header((string) $header);

        return $this;
    }

    /**
     * @param string $url
     *
     * @throws RuntimeException
     * @return Response
     */
    public function redirect($url)
    {
        if (headers_sent()) {
            throw new RuntimeException('Unable to issue redirect header. Headers have already been sent.');
        }

        return $this->header(new Header(Header::LOCATION, $url));
    }

    /**
     * @param Status $status
     * @param string $content
     *
     * @return Response
     */
    public function respond(Status $status, $content = '')
    {
        $this->header($status);

        if (!empty($content)) {
            $this->send($content);
        }

        return $this;
    }

    /**
     * Send the given output using the given or default output strategy.
     *
     * @param string                  $output
     * @param OutputStrategyInterface $outputStrategy
     *
     * @return int
     */
    public function send($output, OutputStrategyInterface $outputStrategy = null)
    {
        if (!($outputStrategy instanceof OutputStrategyInterface)) {
            $outputStrategy = $this->defaultOutputStrategy;
        }

        $contentType = new Header(
            Header::CONTENT_TYPE,
            $outputStrategy->getMime()
        );

        $this->header($contentType);

        return $outputStrategy->send($output);
    }

    /**
     * Directly output results from a file. This bypasses the output strategy altogether.
     *
     * @param $src
     *
     * @throws RuntimeException
     * @return int|bool
     */
    public function sendFile($src)
    {
        $pointer = fopen($src, 'r');

        if (!is_resource($pointer)) {
            throw new RuntimeException("Unable to load '$src' for sending.");
        }

        $result = fpassthru($pointer);

        fclose($pointer);

        return $result;
    }

    /**
     * @param OutputStrategyInterface $defaultOutputStrategy
     *
     * @return Response
     */
    public function setDefaultOutputStrategy($defaultOutputStrategy)
    {
        $this->defaultOutputStrategy = $defaultOutputStrategy;

        return $this;
    }
}
