<?php

namespace Web\Response\Abstraction;

interface StatusInterface 
{
    /**
     * Retrieve the status code
     *
     * @return int
     */
    public function getStatusCode();

    /**
     * Retrieve the status text
     *
     * @return string
     */
    public function getStatusText();
}
