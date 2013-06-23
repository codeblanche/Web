<?php

namespace Web\Response\Output;

use Web\Response\Output\Abstraction\OutputStrategyInterface;

class OutputHTML implements OutputStrategyInterface
{
    /**
     * Output a value for debugging purposes (As a comment for example)
     *
     * @param mixed $value
     *
     * @return int Length of the output
     */
    public function debug($value)
    {
        $output = (string) $value;

        return $this->send('<!-- ' . $output . ' -->');
    }

    /**
     * Output the given content.
     *
     * @param mixed $output
     *
     * @return int Length of the output
     */
    public function send($output)
    {
        echo $output;

        return strlen($output);
    }

    /**
     * Retrieve the mime type for the output strategy
     *
     * @return string
     */
    public function getMime()
    {
        return 'text/html';
    }
}
