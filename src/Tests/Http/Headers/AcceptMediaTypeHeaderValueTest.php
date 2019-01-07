<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (c) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Net\Tests\Http\Headers;

use InvalidArgumentException;
use Opulence\Collections\ImmutableHashTable;
use Opulence\Collections\KeyValuePair;
use Opulence\Net\Http\Headers\AcceptMediaTypeHeaderValue;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Accept media type header value
 */
class AcceptMediaTypeHeaderValueTest extends TestCase
{
    public function qualityScoreOutsideAcceptedRangeProvider(): array
    {
        return [
            ['-1'],
            ['1.5'],
        ];
    }

    /**
     * @dataProvider qualityScoreOutsideAcceptedRangeProvider
     */
    public function testExceptionThrownWithQualityScoreOutsideAcceptedRange($invalidScore): void
    {
        $parameters = new ImmutableHashTable([new KeyValuePair('q', $invalidScore)]);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Quality score must be between 0 and 1, inclusive');
        new AcceptMediaTypeHeaderValue('foo/bar', $parameters);
    }

    public function testGettingQualityReturnsCorrectQuality(): void
    {
        $parameters = new ImmutableHashTable([new KeyValuePair('q', '.5')]);
        $value = new AcceptMediaTypeHeaderValue('foo/bar', $parameters);
        $this->assertEquals(.5, $value->getQuality());
    }

    public function testQualityDefaultsToOne(): void
    {
        $value = new AcceptMediaTypeHeaderValue('foo/bar', null);
        $this->assertEquals(1, $value->getQuality());
    }
}
