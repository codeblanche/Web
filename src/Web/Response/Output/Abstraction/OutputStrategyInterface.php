<?php

namespace Web\Response\Output\Abstraction;

interface OutputStrategyInterface 
{
    /**
     * Output a value for debugging purposes (As a comment for example)
     *
     * @param mixed $value
     *
     * @return int Length of the output
     */
    public function debug($value);

    /**
     * Output the given content.
     *
     * @param mixed $output
     *
     * @return int Length of the output
     */
    public function send($output);

    /**
     * Retrieve the mime type for the output strategy
     *
     * @return string
     */
    public function getMime();
}
