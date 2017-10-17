<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Net\Tests\Http;

use Opulence\Collections\KeyValuePair;
use Opulence\Net\Http\HttpHeaders;

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
        $this->headers->add('foo', 'bar');
        $this->assertEquals(['bar'], $this->headers->get('foo'));
    }

    /**
     * Tests checking if a header exists
     */
    public function testCheckingIfHeaderExists() : void
    {
        $this->assertFalse($this->headers->containsKey('foo'));
        $this->headers->add('foo', 'bar');
        $this->assertTrue($this->headers->containsKey('foo'));
    }

    /**
     * Tests getting all values for a header returns a list of values
     */
    public function testGettingAllValuesForHeaderReturnsListOfValues()
    {
        $this->headers->add('foo', ['bar', 'baz']);
        $this->assertEquals(['bar', 'baz'], $this->headers->get('foo'));
    }

    /**
     * Tests returning only the first value
     */
    public function testGettingFirstValue()
    {
        $this->headers->add('foo', ['bar', 'baz']);
        $this->assertEquals('bar', $this->headers->getFirst('foo', null));
    }

    /**
     * Tests returning only the first value when the key does not exist
     */
    public function testGettingFirstValueWhenKeyDoesNotExist()
    {
        $this->assertEquals('foo', $this->headers->getFirst('THIS_DOES_NOT_EXIST', 'foo'));
    }

    /**
     * Tests that all names are normalized
     */
    public function testNamesAreNormalized() : void
    {
        // Test lower-case names
        $this->headers->add('foo', 'bar');
        $this->assertEquals(['bar'], $this->headers->get('Foo'));
        $this->assertEquals('bar', $this->headers->getFirst('foo'));
        $this->assertTrue($this->headers->containsKey('foo'));
        $this->headers->removeKey('foo');
        // Test snake-case names
        $this->headers->add('FOO_BAR', 'baz');
        $this->assertEquals(['baz'], $this->headers->get('Foo-Bar'));
        $this->assertEquals('baz', $this->headers->getFirst('FOO_BAR'));
        $this->assertTrue($this->headers->containsKey('FOO_BAR'));
        $this->headers->removeKey('FOO_BAR');
        // Test upper-case names
        $this->assertEquals([], $this->headers->toArray());
        $this->headers->add('BAZ', 'blah');
        $this->assertEquals(['blah'], $this->headers->get('Baz'));
        $this->assertEquals('blah', $this->headers->getFirst('BAZ'));
        $this->assertTrue($this->headers->containsKey('BAZ'));
        $this->headers->removeKey('BAZ');
        $this->assertEquals([], $this->headers->toArray());
    }

    /**
     * Tests removing a header
     */
    public function testRemovingHeader() : void
    {
        $this->headers->add('foo', 'bar');
        $this->headers->removeKey('foo');
        $this->assertFalse($this->headers->containsKey('foo'));
    }

    /**
     * Tests setting a header and appending it appends it
     */
    public function testSettingHeaderAndAppendingItAppendsIt() : void
    {
        $this->headers->add('foo', 'bar');
        $this->headers->add('foo', 'baz', true);
        $this->assertEquals(['bar', 'baz'], $this->headers->get('foo'));
    }

    /**
     * Tests setting a header without appending it appends it
     */
    public function testSettingHeaderWithoutAppendingReplacesIt() : void
    {
        $this->headers->add('foo', 'bar');
        $this->headers->add('foo', 'baz', false);
        $this->assertEquals(['baz'], $this->headers->get('foo'));
    }

    /**
     * Tests that getting the headers as an array returns a list of key-value pairs
     */
    public function testToArrayReturnsListOfKeyValuePairs() : void
    {
        $this->headers->add('foo', 'bar');
        $actualValues = [];

        foreach ($this->headers->toArray() as $key => $value) {
            // Verify that the key is numeric, not associative
            $this->assertTrue(is_int($key));
            $this->assertInstanceOf(KeyValuePair::class, $value);
            $actualValues[$value->getKey()] = $value->getValue();
        }

        $this->assertCount(1, $actualValues);
        // The header name will be normalized
        $this->assertEquals(['bar'], $actualValues['Foo']);
    }
}