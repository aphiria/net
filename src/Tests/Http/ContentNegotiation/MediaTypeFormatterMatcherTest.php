<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (c) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Net\Tests\Http\Formatting;

use InvalidArgumentException;
use Opulence\Collections\ImmutableHashTable;
use Opulence\Collections\KeyValuePair;
use Opulence\Net\Http\ContentNegotiation\MediaTypeFormatterMatcher;
use Opulence\Net\Http\ContentNegotiation\MediaTypeFormatters\IMediaTypeFormatter;
use Opulence\Net\Http\Headers\AcceptMediaTypeHeaderValue;
use Opulence\Net\Http\Headers\ContentTypeHeaderValue;
use Opulence\Net\Tests\Http\Formatting\Mocks\User;
use PHPUnit\Framework\TestCase;

/**
 * Tests the media type formatter matcher
 */
class MediaTypeFormatterMatcherTest extends TestCase
{
    /** @var MediaTypeFormatterMatcher The matcher to use in tests */
    private $matcher;

    public function setUp(): void
    {
        $this->matcher = new MediaTypeFormatterMatcher();
    }

    public function testBestFormatterCanMatchWithWildcardSubType(): void
    {
        $formatter1 = $this->createFormatterMock(['application/json'], 1);
        $formatter1->expects($this->once())
            ->method('canWriteType')
            ->with(User::class)
            ->willReturn(true);
        $formatter2 = $this->createFormatterMock(['text/html'], 1);
        $formatter2->expects($this->once())
            ->method('canWriteType')
            ->with(User::class)
            ->willReturn(true);
        $acceptHeader = new AcceptMediaTypeHeaderValue('text/*');
        $match = $this->matcher->getBestResponseMediaTypeFormatterMatch(
            User::class,
            [$formatter1, $formatter2],
            [$acceptHeader]
        );
        $this->assertSame($formatter2, $match->getFormatter());
        $this->assertEquals('text/html', $match->getMediaType());
    }

    public function testBestFormatterCanMatchWithWildcardType(): void
    {
        $formatter1 = $this->createFormatterMock(['application/json'], 1);
        $formatter1->expects($this->once())
            ->method('canWriteType')
            ->with(User::class)
            ->willReturn(true);
        $formatter2 = $this->createFormatterMock(['text/html'], 0);
        $acceptHeader = new AcceptMediaTypeHeaderValue('*/*');
        $match = $this->matcher->getBestResponseMediaTypeFormatterMatch(
            User::class,
            [$formatter1, $formatter2],
            [$acceptHeader]
        );
        $this->assertSame($formatter1, $match->getFormatter());
        $this->assertEquals('application/json', $match->getMediaType());
    }

    public function testBestFormatterIsSelectedByMatchingSupportedMediaTypesInContentTypeHeader(): void
    {
        $formatter1 = $this->createFormatterMock(['application/json'], 1);
        $formatter1->expects($this->once())
            ->method('canReadType')
            ->with(User::class)
            ->willReturn(true);
        $formatter2 = $this->createFormatterMock(['text/html'], 1);
        $formatter2->expects($this->once())
            ->method('canReadType')
            ->with(User::class)
            ->willReturn(true);
        $contentTypeHeader = new ContentTypeHeaderValue('text/html');
        $match = $this->matcher->getBestRequestMediaTypeFormatterMatch(
            User::class,
            [$formatter1, $formatter2],
            $contentTypeHeader
        );
        $this->assertSame($formatter2, $match->getFormatter());
        $this->assertEquals('text/html', $match->getMediaType());
    }

    public function testBestFormatterMatchesMostSpecificMediaTypeWithEqualQualityMediaTypes(): void
    {
        $formatter1 = $this->createFormatterMock(['text/plain'], 1);
        $formatter1->expects($this->once())
            ->method('canWriteType')
            ->with(User::class)
            ->willReturn(true);
        $formatter2 = $this->createFormatterMock(['text/xml'], 1);
        $formatter2->expects($this->once())
            ->method('canWriteType')
            ->with(User::class)
            ->willReturn(true);
        $formatter3 = $this->createFormatterMock(['text/html'], 1);
        $formatter3->expects($this->once())
            ->method('canWriteType')
            ->with(User::class)
            ->willReturn(true);
        $acceptHeaders = [
            new AcceptMediaTypeHeaderValue('*/*'),
            new AcceptMediaTypeHeaderValue('text/*'),
            new AcceptMediaTypeHeaderValue('text/html')
        ];
        $match = $this->matcher->getBestResponseMediaTypeFormatterMatch(
            User::class,
            [$formatter1, $formatter2, $formatter3],
            $acceptHeaders
        );
        $this->assertSame($formatter3, $match->getFormatter());
        $this->assertEquals('text/html', $match->getMediaType());
    }

    public function testBestFormatterMatchesWildcardSubTypeWithHigherQualityScoreThanSpecificMediaType(): void
    {
        $formatter = $this->createFormatterMock(['text/plain', 'text/html'], 1);
        $formatter->expects($this->once())
            ->method('canWriteType')
            ->with(User::class)
            ->willReturn(true);
        $acceptHeaders = [
            new AcceptMediaTypeHeaderValue('text/*', new ImmutableHashTable([new KeyValuePair('q', 0.5)])),
            new AcceptMediaTypeHeaderValue('text/html', new ImmutableHashTable([new KeyValuePair('q', 0.3)]))
        ];
        $match = $this->matcher->getBestResponseMediaTypeFormatterMatch(
            User::class,
            [$formatter],
            $acceptHeaders
        );
        $this->assertSame($formatter, $match->getFormatter());
        $this->assertEquals('text/plain', $match->getMediaType());
    }

    public function tesBestFormatterMatchesWildcardTypeWithHigherQualityScoreThanSpecificMediaType(): void
    {
        $formatter = $this->createFormatterMock(['application/json', 'text/html'], 1);
        $formatter->expects($this->once())
            ->method('canWriteType')
            ->with(User::class)
            ->willReturn(true);
        $acceptHeaders = [
            new AcceptMediaTypeHeaderValue('*/*', new ImmutableHashTable([new KeyValuePair('q', 0.5)])),
            new AcceptMediaTypeHeaderValue('text/html', new ImmutableHashTable([new KeyValuePair('q', 0.3)]))
        ];
        $match = $this->matcher->getBestResponseMediaTypeFormatterMatch(
            User::class,
            [$formatter],
            $acceptHeaders
        );
        $this->assertSame($formatter, $match->getFormatter());
        $this->assertEquals('application/json', $match->getMediaType());
    }

    public function testBestFormatterThatMatchesZeroQualityMediaTypeReturnsNullMatch(): void
    {
        // The media type should be filtered out of the list of media types to check against
        $formatter = $this->createFormatterMock(['text/html'], 0);
        $acceptHeader = new AcceptMediaTypeHeaderValue(
            'text/html',
            new ImmutableHashTable([new KeyValuePair('q', 0.0)])
        );
        $match = $this->matcher->getBestResponseMediaTypeFormatterMatch(
            User::class,
            [$formatter],
            [$acceptHeader]
        );
        $this->assertNull($match);
    }

    public function testBestFormatterWithInvalidMediaTypeThrowsException(): void
    {
        $formatter = $this->createMock(IMediaTypeFormatter::class);

        try {
            $acceptHeader = new AcceptMediaTypeHeaderValue('text');
            $this->matcher->getBestResponseMediaTypeFormatterMatch(User::class, [$formatter], [$acceptHeader]);
            $this->fail('"text" is not a valid media type');
        } catch (InvalidArgumentException $ex) {
            $this->assertTrue(true);
        }

        try {
            $acceptHeader = new AcceptMediaTypeHeaderValue('text/');
            $this->matcher->getBestResponseMediaTypeFormatterMatch(User::class, [$formatter], [$acceptHeader]);
            $this->fail('"text/" is not a valid media type');
        } catch (InvalidArgumentException $ex) {
            $this->assertTrue(true);
        }

        try {
            $acceptHeader = new AcceptMediaTypeHeaderValue('/html');
            $this->matcher->getBestResponseMediaTypeFormatterMatch(User::class, [$formatter], [$acceptHeader]);
            $this->fail('"/html" is not a valid media type');
        } catch (InvalidArgumentException $ex) {
            $this->assertTrue(true);
        }
    }

    public function testBestRequestFormatterIsSkippedIfItCannotReadType(): void
    {
        $formatter1 = $this->createFormatterMock(['application/json'], 1);
        $formatter1->expects($this->once())
            ->method('canReadType')
            ->with(User::class)
            ->willReturn(false);
        $formatter2 = $this->createFormatterMock(['text/html'], 1);
        $formatter2->expects($this->once())
            ->method('canReadType')
            ->with(User::class)
            ->willReturn(true);
        $match = $this->matcher->getBestRequestMediaTypeFormatterMatch(
            User::class,
            [$formatter1, $formatter2],
            new ContentTypeHeaderValue('*/*')
        );
        $this->assertSame($formatter2, $match->getFormatter());
        $this->assertEquals('text/html', $match->getMediaType());
    }

    public function testBestResponseFormatterIsSkippedIfItCannotWriteType(): void
    {
        $formatter1 = $this->createFormatterMock(['application/json'], 1);
        $formatter1->expects($this->once())
            ->method('canWriteType')
            ->with(User::class)
            ->willReturn(false);
        $formatter2 = $this->createFormatterMock(['text/html'], 1);
        $formatter2->expects($this->once())
            ->method('canWriteType')
            ->with(User::class)
            ->willReturn(true);
        $acceptHeader = new AcceptMediaTypeHeaderValue('*/*');
        $match = $this->matcher->getBestResponseMediaTypeFormatterMatch(
            User::class,
            [$formatter1, $formatter2],
            [$acceptHeader]
        );
        $this->assertSame($formatter2, $match->getFormatter());
        $this->assertEquals('text/html', $match->getMediaType());
    }

    /**
     * Creates a mock media type formatter with a list of supported media types
     *
     * @param array $supportedMediaTypes The list of supported media types
     * @param int $numTimesSupportedMediaTypesCalled The number of times the formatter's supported media types will be checked
     * @return IMediaTypeFormatter|\PHPUnit_Framework_MockObject_MockObject The mocked formatter
     */
    private function createFormatterMock(
        array $supportedMediaTypes,
        int $numTimesSupportedMediaTypesCalled
    ): IMediaTypeFormatter {
        $formatter = $this->createMock(IMediaTypeFormatter::class);
        $formatter->expects($this->exactly($numTimesSupportedMediaTypesCalled))
            ->method('getSupportedMediaTypes')
            ->willReturn($supportedMediaTypes);

        return $formatter;
    }
}
