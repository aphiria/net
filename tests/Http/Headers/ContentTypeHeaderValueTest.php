<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2023 David Young
 * @license   https://github.com/aphiria/aphiria/blob/1.x/LICENSE.md
 */

declare(strict_types=1);

namespace Aphiria\Net\Tests\Http\Headers;

use Aphiria\Collections\IImmutableDictionary;
use Aphiria\Collections\ImmutableHashTable;
use Aphiria\Collections\KeyValuePair;
use Aphiria\Net\Http\Headers\ContentTypeHeaderValue;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class ContentTypeHeaderValueTest extends TestCase
{
    public function testGettingCharsetReturnsOneSetInConstructor(): void
    {
        $parameters = new ImmutableHashTable([new KeyValuePair('charset', 'utf-8')]);
        $value = new ContentTypeHeaderValue('foo/bar', $parameters);
        $this->assertSame('utf-8', $value->charset);
    }

    public function testGettingMediaTypeReturnsOneSetInConstructor(): void
    {
        $parameters = new ImmutableHashTable([new KeyValuePair('charset', 'utf-8')]);
        $value = new ContentTypeHeaderValue('foo/bar', $parameters);
        $this->assertSame('foo/bar', $value->mediaType);
    }

    public function testGettingSubTypeReturnsCorrectSubtType(): void
    {
        /** @var IImmutableDictionary<string, string|null> $parameters */
        $parameters = $this->createMock(IImmutableDictionary::class);
        $value = new ContentTypeHeaderValue('foo/bar', $parameters);
        $this->assertSame('bar', $value->subType);
    }

    public function testGettingTypeReturnsCorrectType(): void
    {
        /** @var IImmutableDictionary<string, string|null> $parameters */
        $parameters = $this->createMock(IImmutableDictionary::class);
        $value = new ContentTypeHeaderValue('foo/bar', $parameters);
        $this->assertSame('foo', $value->type);
    }

    public function testTypeWithSuffixSetsTypeSubTypeAndSuffixesCorrectly(): void
    {
        /** @var IImmutableDictionary<string, string|null> $parameters */
        $parameters = $this->createMock(IImmutableDictionary::class);
        $value = new ContentTypeHeaderValue('application/foo+json', $parameters);
        $this->assertSame('application', $value->type);
        $this->assertSame('foo+json', $value->subType);
        $this->assertSame('foo', $value->getSubTypeWithoutSuffix());
        $this->assertSame('json', $value->suffix);
    }

    /**
     * @param string $incorrectlyFormattedMediaType The incorrectly-formatted media type
     */
    #[TestWith(['foo'])]
    #[TestWith(['foo/'])]
    public function testIncorrectlyFormattedMediaTypeThrowsException(string $incorrectlyFormattedMediaType): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Media type must be in format {type}/{sub-type}, received {$incorrectlyFormattedMediaType}");
        /** @var IImmutableDictionary<string, string|null> $parameters */
        $parameters = $this->createMock(IImmutableDictionary::class);
        new ContentTypeHeaderValue($incorrectlyFormattedMediaType, $parameters);
    }
}
