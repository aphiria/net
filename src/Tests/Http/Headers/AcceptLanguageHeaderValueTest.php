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
use Opulence\Collections\IImmutableDictionary;
use Opulence\Collections\ImmutableHashTable;
use Opulence\Collections\KeyValuePair;
use Opulence\Net\Http\Headers\AcceptLanguageHeaderValue;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Accept-Language header value
 */
class AcceptLanguageHeaderValueTest extends TestCase
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
        new AcceptLanguageHeaderValue('en-US', $parameters);
    }

    public function testGettingLanguageReturnsSameOneSetInConstructor(): void
    {
        $parameters = $this->createMock(IImmutableDictionary::class);
        $value = new AcceptLanguageHeaderValue('en-US', $parameters);
        $this->assertSame('en-US', $value->getLanguage());
    }

    public function testGettingParametersReturnsSameOneSetInConstructor(): void
    {
        $parameters = new ImmutableHashTable([]);
        $value = new AcceptLanguageHeaderValue('en-US', $parameters);
        $this->assertSame($parameters, $value->getParameters());
    }

    public function testGettingQualityReturnsCorrectQuality(): void
    {
        $parameters = new ImmutableHashTable([new KeyValuePair('q', '.5')]);
        $value = new AcceptLanguageHeaderValue('en-US', $parameters);
        $this->assertEquals(.5, $value->getQuality());
    }

    public function testQualityDefaultsToOne(): void
    {
        $value = new AcceptLanguageHeaderValue('en-US', null);
        $this->assertEquals(1, $value->getQuality());
    }
}
