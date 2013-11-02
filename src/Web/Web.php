<?php

namespace Web;

use Web\Request\Request;
use Web\Response\Response;
use Web\Route\Router;

class Web
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Router
     */
    protected $router;

    /**
     * Default constructor.
     *
     * @param Request  $request
     * @param Response $response
     * @param Router   $router
     */
    function __construct(Request $request, Response $response, Router $router)
    {
        $this->request  = $request;
        $this->response = $response;
        $this->router   = $router;
    }

    /**
     * @return \Web\Request\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \Web\Response\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return \Web\Route\Router
     */
    public function getRouter()
    {
        return $this->router;
    }
}
