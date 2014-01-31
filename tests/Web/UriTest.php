<?php

namespace Web;

class UriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Uri
     */
    protected $uri;

    public function setUp()
    {
        $url = 'http://user:password@randomhostname/this/is/a/path/filename.ext?qs1=one&qs2=two#frag';

        $this->uri = new Uri($url);
    }

    public function testImport()
    {
        $uri    = new Uri();
        $result = $uri->import('http://user:password@randomhostname/this/is/a/path/filename.ext?qs1=one&qs2=two#frag');

        $this->assertInstanceOf('Web\Uri', $result);
        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('user', $uri->getUser());
        $this->assertEquals('password', $uri->getPass());
        $this->assertEquals('randomhostname', $uri->getHost());
        $this->assertEquals('', $uri->getPort());
        $this->assertEquals('/this/is/a/path/filename.ext', $uri->getPath());
        $this->assertEquals('/this/is/a/path', $uri->getDirname());
        $this->assertEquals('filename.ext', $uri->getBasename());
        $this->assertEquals('ext', $uri->getExtension());
        $this->assertEquals('filename', $uri->getFilename());
        $this->assertInstanceOf('Web\QueryString', $uri->getQuery());

        $arrImport = array(
            'scheme'   => 'https',
            'host'     => 'differenthost',
            'port'     => '81',
            'user'     => 'anonymous',
            'pass'     => 'anotherpassword',
            'path'     => '/root/file.txt',
            'fragment' => 'fragmentation',
        );

        $uri->import($arrImport);

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('anonymous', $uri->getUser());
        $this->assertEquals('anotherpassword', $uri->getPass());
        $this->assertEquals('differenthost', $uri->getHost());
        $this->assertEquals('81', $uri->getPort());
        $this->assertEquals('/root/file.txt', $uri->getPath());
        $this->assertEquals('/root', $uri->getDirname());
        $this->assertEquals('file.txt', $uri->getBasename());
        $this->assertEquals('txt', $uri->getExtension());
        $this->assertEquals('file', $uri->getFilename());
        $this->assertInstanceOf('Web\QueryString', $uri->getQuery());

        $uri->import(array());

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('anonymous', $uri->getUser());
        $this->assertEquals('anotherpassword', $uri->getPass());
        $this->assertEquals('differenthost', $uri->getHost());
        $this->assertEquals('81', $uri->getPort());
        $this->assertEquals('/root/file.txt', $uri->getPath());
        $this->assertEquals('/root', $uri->getDirname());
        $this->assertEquals('file.txt', $uri->getBasename());
        $this->assertEquals('txt', $uri->getExtension());
        $this->assertEquals('file', $uri->getFilename());
        $this->assertInstanceOf('Web\QueryString', $uri->getQuery());

        $uri->import('');

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('anonymous', $uri->getUser());
        $this->assertEquals('anotherpassword', $uri->getPass());
        $this->assertEquals('differenthost', $uri->getHost());
        $this->assertEquals('81', $uri->getPort());
        $this->assertEquals('/root/file.txt', $uri->getPath());
        $this->assertEquals('/root', $uri->getDirname());
        $this->assertEquals('file.txt', $uri->getBasename());
        $this->assertEquals('txt', $uri->getExtension());
        $this->assertEquals('file', $uri->getFilename());
        $this->assertInstanceOf('Web\QueryString', $uri->getQuery());

        $uri->import('http://user:password@randomhostname/');

        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('user', $uri->getUser());
        $this->assertEquals('password', $uri->getPass());
        $this->assertEquals('randomhostname', $uri->getHost());
        $this->assertEquals('81', $uri->getPort());
        $this->assertEquals('', $uri->getPath());
        $this->assertEquals('', $uri->getDirname());
        $this->assertEquals('', $uri->getBasename());
        $this->assertEquals('', $uri->getExtension());
        $this->assertEquals('', $uri->getFilename());
        $this->assertInstanceOf('Web\QueryString', $uri->getQuery());
    }

    public function testSetGetBasename()
    {
        $baseName = 'test-base-name.ext';
        $this->uri->setBasename($baseName);
        $this->assertEquals($baseName, $this->uri->getBasename());
    }

    public function testSetGetDirname()
    {
        $dirName = '/test/dir/name';
        $this->uri->setDirname($dirName);
        $this->assertEquals($dirName, $this->uri->getDirname());
    }

    public function testSetGetExtension()
    {
        $extensions = array('', 'extension');

        foreach ($extensions as $extension) {
            $this->uri->setExtension($extension);
            $this->assertEquals($extension, $this->uri->getExtension());
        }
    }

    public function testSetGetFilename()
    {
        $filename = 'test-file-name';
        $this->uri->setFilename($filename);
        $this->assertEquals($filename, $this->uri->getFilename());
    }

    public function testSetGetFragment()
    {
        $fragment = 'fragmentation';
        $this->uri->setFragment($fragment);
        $this->assertEquals($fragment, $this->uri->getFragment());
    }

    public function testSetGetHost()
    {
        $host = 'somenewhost';
        $this->uri->setHost($host);
        $this->assertEquals($host, $this->uri->getHost());
    }

    public function testSetGetPass()
    {
        $pass = 'supercalifragilisticexpialidocious';
        $this->uri->setPass($pass);
        $this->assertEquals($pass, $this->uri->getPass());
    }

    public function testSetGetPath()
    {
        $path = 'this/is/another/path/with-file.other';
        $this->uri->setPath($path);
        $this->assertEquals($path, $this->uri->getPath());
        $this->assertEquals('this/is/another/path', $this->uri->getDirname());
        $this->assertEquals('with-file.other', $this->uri->getBasename());
        $this->assertEquals('with-file', $this->uri->getFilename());
        $this->assertEquals('other', $this->uri->getExtension());
        $this->uri->setPath('');
        $this->assertEquals('', $this->uri->getDirname());
        $this->assertEquals('', $this->uri->getBasename());
        $this->assertEquals('', $this->uri->getFilename());
        $this->assertEquals('', $this->uri->getExtension());
    }

    public function testSetGetPort()
    {
        $port = '8080';
        $this->uri->setPort($port);
        $this->assertEquals($port, $this->uri->getPort());
    }

    public function testSetGetQuery()
    {
        $this->assertInstanceOf('Web\QueryString', $this->uri->getQuery());
        $expected = 'one=1&two=2';
        $this->uri->setQuery($expected);
        $this->assertEquals($expected, (string) $this->uri->getQuery());
        $this->uri->setQuery(new QueryString());
        $this->assertEmpty((string) $this->uri->getQuery());
    }

    public function testSetGetScheme()
    {
        $scheme = 'ftp';
        $this->uri->setScheme($scheme);
        $this->assertEquals($scheme, $this->uri->getScheme());
    }

    public function testSetGetUser()
    {
        $user = 'anewuser';
        $this->uri->setUser($user);
        $this->assertEquals($user, $this->uri->getUser());
    }

    public function testToArray()
    {
        $uri      = new Uri('http://user:password@randomhostname/this/is/a/path/filename.ext?qs1=one&qs2=two#frag');
        $actual   = $uri->toArray();
        $expected = array(
            'scheme'    => 'http',
            'host'      => 'randomhostname',
            'port'      => null,
            'user'      => 'user',
            'pass'      => 'password',
            'path'      => '/this/is/a/path/filename.ext',
            'basename'  => 'filename.ext',
            'dirname'   => '/this/is/a/path',
            'extension' => 'ext',
            'filename'  => 'filename',
            'query'     => 'qs1=one&qs2=two',
            'fragment'  => 'frag',
        );

        $this->assertTrue(is_array($actual));
        $this->assertEquals($expected, $actual);
    }

    public function testToString()
    {
        $expected = 'http://user:password@randomhostname:81/this/is/a/path/filename.ext?qs1=one&qs2=two#frag';
        $uri      = new Uri($expected);
        $actual   = $uri->toString();

        $this->assertEquals($expected, $actual);
        $this->assertEquals($expected, (string) $uri);
    }
}
