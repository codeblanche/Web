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
        $this->request      = new Request(null, 'PGCHE');
        $_POST['post1']     = 'post1';
        $_GET['get1']       = 'get1';
        $_COOKIE['cookie1'] = 'cookie1';
        $_SERVER['server1'] = 'server1';
        $_ENV['env1']       = 'env1';
        $_FILES['files1']   = 'files1';
        $_FILES['files3']   = array('tmp_name' => '', 'size' => '');
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
        $files1 = $this->request->files('files1');

        $this->assertNotNull($files1);
        $this->assertTrue(is_array($files1));
        $this->assertTrue(in_array($_FILES['files1'], $files1));

        $files2 = $this->request->files('files2');

        $this->assertNotNull($files2);
        $this->assertTrue(is_array($files2));
        $this->assertTrue(empty($files2));

        $files3 = $this->request->files('files3');

        $this->assertNotNull($files3);
        $this->assertTrue(is_array($files3));
        $this->assertTrue(!empty($files3));
        $this->assertArrayHasKey('tmp_name', $files3);
        $this->assertArrayHasKey('size', $files3);
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
        $this->assertArrayHasKey('post1', $this->request->post());
    }

    public function testPut()
    {
        $file = __DIR__ . '/../../fixtures/php-input.dat';
        $this->assertNull($this->request->put(null, false));
        $this->assertEquals('', $this->request->put(null, true));
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
