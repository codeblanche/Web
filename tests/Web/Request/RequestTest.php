<?php

namespace Web\Request;

use InvalidArgumentException;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
    protected $request;

    public function setUp()
    {
        $this->request      = new Request();
        $_POST['post1']     = 'post1';
        $_GET['get1']       = 'get1';
        $_COOKIE['cookie1'] = 'cookie1';
        $_SERVER['server1'] = 'server1';
        $_ENV['env1']       = 'env1';
        $_FILES['files1']   = 'files1';
    }

    public function testCookie()
    {
        $this->assertEquals('cookie1', $this->request->cookie('cookie1'));
        $this->assertNull($this->request->cookie('cookie2'));
    }

    public function testEnv()
    {
        $this->assertEquals('env1', $this->request->env('env1'));
        $this->assertNull($this->request->env('env2'));
    }

    public function testFiles()
    {
        $this->assertEquals('files1', $this->request->files('files1'));
        $this->assertNull($this->request->files('files2'));
    }

    public function testGet()
    {
        $this->assertEquals('get1', $this->request->get('get1'));
        $this->assertNull($this->request->get('get2'));
    }

    public function testGetUri()
    {
        $this->assertInstanceOf('Web\Uri', $this->request->uri());
    }

    public function testPost()
    {
        $this->assertEquals('post1', $this->request->post('post1'));
        $this->assertNull($this->request->post('post2'));
    }

    public function testPut()
    {
        $file = __DIR__ . '/../../fixtures/php-input.dat';
        $this->assertNull($this->request->put());
        $this->assertEquals(file_get_contents($file), $this->request->put($file));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testPutException()
    {
        $this->request->put('somefakeresourcepath.ext');
    }

    public function testServer()
    {
        $this->assertEquals('server1', $this->request->server('server1'));
        $this->assertNull($this->request->server('server2'));
    }

    public function testValue()
    {
        $this->assertEquals('env1', $this->request->value('env1'));
        $this->assertNull($this->request->value('env2'));
    }
}
