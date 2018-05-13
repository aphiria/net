<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2018 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Net\Tests\Http\Formatting\Serialization;

use Opulence\Net\Http\Formatting\Serialization\TypeResolver;
use Opulence\Net\Tests\Http\Formatting\Serialization\Mocks\User;

/**
 * Tests the type resolver
 */
class TypeResolverTest extends \PHPUnit\Framework\TestCase
{
    public function testResolvingTypeForObjectUsesObjectsClassName(): void
    {
        $this->assertEquals(User::class, TypeResolver::resolveType(new User(123, 'foo@bar.com')));
    }

    public function testResolvingTypeForScalarUsesScalarType(): void
    {
        $this->assertEquals('array', TypeResolver::resolveType([1]));
        $this->assertEquals('boolean', TypeResolver::resolveType(true));
        $this->assertEquals('integer', TypeResolver::resolveType(1));
        $this->assertEquals('double', TypeResolver::resolveType(1.5));
        $this->assertEquals('string', TypeResolver::resolveType('foo'));
    }
}