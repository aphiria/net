<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Net\Http;

/**
 * Tests the HTTP headers
 */
class HttpHeadersTest extends \PHPUnit\Framework\TestCase
{
    /** @var HttpHeaders The headers to use */
    private $headers = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->headers = new HttpHeaders();
    }

    /**
     * Tests setting a string value
     */
    public function testAddingStringValue()
    {
        $this->headers->set('foo', 'bar');
        $this->assertEquals('bar', $this->headers->get('foo'));
    }

    /**
     * Tests checking if a header exists
     */
    public function testCheckingIfHeaderExists() : void
    {
        $this->assertFalse($this->headers->has('foo'));
        $this->headers->set('foo', 'bar');
        $this->assertTrue($this->headers->has('foo'));
    }

    /**
     * Tests getting all values
     */
    public function testGettingAll() : void
    {
        $this->headers->set('foo', 'bar');
        $this->assertEquals(['FOO' => ['bar']], $this->headers->getAll());
    }

    /**
     * Tests getting all values for a header returns a list of values
     */
    public function testGettingAllValuesForHeaderReturnsListOfValues()
    {
        $this->headers->set('foo', ['bar', 'baz']);
        $this->assertEquals(['bar', 'baz'], $this->headers->get('foo', null, false));
    }

    /**
     * Tests returning only the first value
     */
    public function testGettingFirstValue()
    {
        $this->headers->set('foo', ['bar', 'baz']);
        $this->assertEquals('bar', $this->headers->get('foo', null, true));
    }

    /**
     * Tests returning only the first value when the key does not exist
     */
    public function testGettingFirstValueWhenKeyDoesNotExist()
    {
        $this->assertEquals('foo', $this->headers->get('THIS_DOES_NOT_EXIST', 'foo', true));
    }

    /**
     * Tests that all names are normalized
     */
    public function testNamesAreNormalized() : void
    {
        $this->headers->set('foo', 'bar');
        $this->assertEquals('bar', $this->headers->get('foo'));
        $this->assertEquals(['FOO' => ['bar']], $this->headers->getAll());
        $this->assertTrue($this->headers->has('foo'));
        $this->headers->remove('foo');
        $this->assertEquals([], $this->headers->getAll());
        $this->headers->set('BAZ', 'blah');
        $this->assertEquals('blah', $this->headers->get('BAZ'));
        $this->assertEquals(['BAZ' => ['blah']], $this->headers->getAll());
        $this->assertTrue($this->headers->has('BAZ'));
        $this->headers->remove('BAZ');
        $this->assertEquals([], $this->headers->getAll());
    }

    /**
     * Tests removing a header
     */
    public function testRemovingHeader() : void
    {
        $this->headers->set('foo', 'bar');
        $this->headers->remove('foo');
        $this->assertFalse($this->headers->has('foo'));
    }

    /**
     * Tests setting a header and replacing it replaces it
     */
    public function testSettingHeaderAndReplacingItReplacesIt() : void
    {
        $this->headers->set('foo', 'bar');
        $this->headers->set('foo', 'baz', true);
        $this->assertEquals('baz', $this->headers->get('foo'));
    }

    /**
     * Tests setting a header without replacing it appends it
     */
    public function testSettingHeaderWithoutReplacingAppendsIt() : void
    {
        $this->headers->set('foo', 'bar');
        $this->headers->set('foo', 'baz', false);
        $this->assertEquals(['bar', 'baz'], $this->headers->get('foo', null, false));
    }
}