<?php

namespace Web\Response;

use Web\Response\Abstraction\OutputStrategyInterface;
use Web\Response\Output\OutputHTML;

class Response
{
    /**
     * @var OutputStrategyInterface
     */
    protected $defaultOutputStrategy;

    /**
     * The HTTP status code of the message.
     *
     * @var int
     */
    protected $status_code_default = 200;

    /**
     * List of default HTTP status messages.
     *
     * @var array
     */
    protected $status_text_default = array(
        '100' => 'Continue',
        '101' => 'Switching Protocols',
        '200' => 'OK',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',
        '300' => 'Multiple Choices',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '303' => 'See Other',
        '304' => 'Not Modified',
        '305' => 'Use Proxy',
        '307' => 'Temporary Redirect',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '402' => 'Payment Required',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Timeout',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Request Entity Too Large',
        '414' => 'Request-URI Too Long',
        '415' => 'Unsupported Media Type',
        '416' => 'Requested Range Not Satisfiable',
        '417' => 'Expectation Failed',
        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad Gateway',
        '503' => 'Service Unavailable',
        '504' => 'Gateway Timeout',
        '505' => 'HTTP Version Not Supported',
    );

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
     * @param string  $name
     * @param string  $value
     * @param integer $duration
     *
     * @throws \BadMethodCallException
     * @return Response
     */
    public function cookie($name, $value, $duration)
    {
        throw new \BadMethodCallException("Method is not yet implemented");

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return Response
     */
    public function header($name, $value)
    {
        header("$name: $value");

        return $this;
    }

    /**
     * @param string $url
     *
     * @return Response
     */
    public function redirect($url)
    {
        if (headers_sent()) {
            echo '<script type="text/javascript"> ';
            echo "location.href = '$url';";
            echo '</script>';

            return;
        }

        return $this->header('location', $url);
    }

    /**
     * @param int    $code
     * @param string $reason
     * @param string $content
     *
     * @return Response
     */
    public function respond($code, $reason = '', $content = '')
    {
        if (!empty($code)) {
            $code = $this->status_code_default;
        }

        if (empty($reason)) {
            $reason = $this->status_text_default[$code];
        }

        header("HTTP/1.0 $code $reason");

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

        $this->header('content-type', $outputStrategy->getMime());

        return $outputStrategy->send($output);
    }

    /**
     * Directly output results from a file. This bypasses the output strategy altogether.
     *
     * @param $src
     *
     * @throws \RuntimeException
     * @return int|bool
     */
    public function sendFile($src)
    {
        $pointer = fopen($src, 'r');

        if (!is_resource($pointer)) {
            throw new \RuntimeException("Unable to load '$src' for sending.");
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

    /**
     * @return OutputStrategyInterface
     */
    public function getDefaultOutputStrategy()
    {
        return $this->defaultOutputStrategy;
    }


}
