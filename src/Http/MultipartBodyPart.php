<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/aphiria/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Aphiria\Net\Http;

/**
 * Defines a multipart body part
 */
class MultipartBodyPart
{
    /** @var HttpHeaders The headers of this body part */
    private HttpHeaders $headers;
    /** @var IHttpBody|null The body of this body part if one is set, otherwise null */
    private ?IHttpBody $body;

    /**
     * @param HttpHeaders $headers The headers of this body part
     * @param IHttpBody|null $body The body of this body part if one is set, otherwise null
     */
    public function __construct(HttpHeaders $headers, ?IHttpBody $body)
    {
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Gets the multipart body part as a string
     * Note: This can be used in raw HTTP messages
     *
     * @return string The body part as a string
     */
    public function __toString(): string
    {
        return "{$this->headers}\r\n\r\n" . ($this->body === null ? '' : (string)$this->body);
    }

    /**
     * Gets the body of this body part
     *
     * @return IHttpBody|null The body of this body part if one is set, otherwise null
     */
    public function getBody(): ?IHttpBody
    {
        return $this->body;
    }

    /**
     * Gets the headers of this body part
     *
     * @return HttpHeaders The headers of this body part
     */
    public function getHeaders(): HttpHeaders
    {
        return $this->headers;
    }
}
