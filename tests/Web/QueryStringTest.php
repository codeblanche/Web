<?php

namespace Web;

class QueryStringTest extends \PHPUnit_Framework_TestCase
{
    protected $array = array(
        'q1' => 'one',
        'q2' => 'two',
        'q3' => 'three',
        'q4' => 'four',
        'q5' => 'five',
        'q6' => 'six',
        'q7' => 'seven',
        'q8' => 'eight',
        'q9' => 'nine',
    );

    /**
     * @var QueryString
     */
    protected $queryString;

    protected $string = 'q1=one&q2=two&q3=three&q4=four&q5=five&q6=six&q7=seven&q8=eight&q9=nine';

    public function setUp()
    {
        $this->queryString = new QueryString($this->string);
    }

    public function testClear()
    {
        $this->assertEquals($this->string, $this->queryString->toString());

        $this->queryString->clear();

        $this->assertEmpty($this->queryString->toString());
        $this->assertEmpty($this->queryString->toArray());
    }

    public function testHas()
    {
        $this->assertFalse($this->queryString->has('somerandomkey'));
        $this->queryString->import($this->array);
        $this->assertTrue($this->queryString->has('q1'));
    }

    public function testImportArray()
    {
        $this->queryString->clear();
        $this->queryString->import(array());
        $this->assertEmpty($this->queryString->toArray());
        $this->queryString->import($this->array);

        foreach ($this->array as $key => $value) {
            $this->assertTrue($this->queryString->has($key));
            $this->assertEquals($value, $this->queryString->get($key));
        }
    }

    public function testImportString()
    {
        $this->queryString->clear();
        $this->queryString->import('');
        $this->assertEmpty($this->queryString->toString());
        $this->queryString->import($this->string);
        $arr = array();

        parse_str($this->string, $arr);

        foreach ($arr as $key => $value) {
            $this->assertTrue($this->queryString->has($key));
            $this->assertEquals($value, $this->queryString->get($key));
        }
    }

    public function testRemove()
    {
        $this->assertTrue($this->queryString->has('q9'));
        $this->queryString->remove('q9');
        $this->assertFalse($this->queryString->has('q9'));
        $this->assertNull($this->queryString->get('q9'));
    }

    public function testSetGet()
    {
        $this->assertFalse($this->queryString->has('q10'));
        $result = $this->queryString->set('q10', 'ten');
        $this->assertInstanceOf('Web\QueryString', $result);
        $this->assertEquals('ten', $this->queryString->get('q10'));
        $this->queryString->import($this->string);
    }

    public function testGetNonExistent()
    {
        $this->assertNull($this->queryString->get('someRandomKey'));
    }

    public function testToArray()
    {
        $array = $this->queryString->toArray();
        $this->assertEquals($this->array, $array);

        $expected = $this->array;
        unset($expected['q9']);
        $this->queryString->set('q9', null);

        $this->assertEquals($expected, $this->queryString->toArray(true));
    }

    public function testToString()
    {
        $this->queryString->import($this->array);
        $this->assertEquals($this->string, (string) $this->queryString);
    }

    public function testVacant()
    {
        $this->queryString->set('q2', null);
        $this->assertFalse($this->queryString->vacant('q1'));
        $this->assertTrue($this->queryString->vacant('q2'));
    }
}
