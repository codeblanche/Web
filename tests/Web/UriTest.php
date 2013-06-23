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
        $this->markTestIncomplete();
    }

    public function testSetGetBasename()
    {
        $this->markTestIncomplete();
    }

    public function testSetGetDirname()
    {
        $this->markTestIncomplete();
    }

    public function testSetGetExtension()
    {
        $this->markTestIncomplete();
    }

    public function testSetGetFilename()
    {
        $this->markTestIncomplete();
    }

    public function testSetGetFragment()
    {
        $this->markTestIncomplete();
    }

    public function testSetGetHost()
    {
        $this->markTestIncomplete();
    }

    public function testSetGetPass()
    {
        $this->markTestIncomplete();
    }

    public function testSetGetPath()
    {
        $this->markTestIncomplete();
    }

    public function testSetGetPort()
    {
        $this->markTestIncomplete();
    }

    public function testSetGetQuery()
    {
        $this->markTestIncomplete();
    }

    public function testSetGetScheme()
    {
        $this->markTestIncomplete();
    }

    public function testSetGetUser()
    {
        $this->markTestIncomplete();
    }

    public function testToArray()
    {
        $this->markTestIncomplete();
    }

    public function testToString()
    {
        $this->markTestIncomplete();
    }
}
