<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/net/blob/master/LICENSE.md
 */

namespace Opulence\Net\Http\Requests;

use InvalidArgumentException;
use Opulence\Collections\HashTable;
use Opulence\Collections\IDictionary;
use Opulence\Net\Http\HttpHeaders;
use Opulence\Net\Http\IHttpBody;
use Opulence\Net\Uri;

/**
 * Defines an HTTP request message
 */
class Request implements IHttpRequestMessage
{
    /** @var string The request method */
    protected $method = '';
    /** @var HttpHeaders The request headers */
    protected $headers = null;
    /** @var IHttpBody|null The request body if there is one, otherwise null */
    protected $body = null;
    /** @var Uri The request URI */
    protected $uri = null;
    /** @var IDictionary The request properties */
    protected $properties = null;
    /** @var string The HTTP protocol version */
    protected $protocolVersion = '';
    /** @var The list of valid HTTP methods */
    private static $validMethod = [
        'CONNECT',
        'DELETE',
        'GET',
        'HEAD',
        'OPTIONS',
        'PATCH',
        'POST',
        'PURGE',
        'PUT',
        'TRACE'
    ];

    /**
     * @param string $method The request method
     * @param Uri $uri The request URI
     * @param HttpHeaders|null $headers The request headers if any are set, otherwise null
     * @param IHttpBody $body The request body
     * @param IDictionary|null $properties The request properties
     * @param string $protocolVersion The HTTP protocol version
     */
    public function __construct(
        string $method,
        Uri $uri,
        HttpHeaders $headers = null,
        ?IHttpBody $body = null,
        IDictionary $properties = null,
        string $protocolVersion = '1.1'
    ) {
        $this->setMethod($method);
        $this->uri = $uri;
        $this->headers = $headers ?? new HttpHeaders();
        $this->body = $body;
        $this->properties = $properties ?? new HashTable();
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * @inheritdoc
     */
    public function __toString() : string
    {
        // Todo
    }

    /**
     * @inheritdoc
     */
    public function getBody() : ?IHttpBody
    {
        return $this->body;
    }

    /**
     * @inheritdoc
     */
    public function getHeaders() : HttpHeaders
    {
        return $this->headers;
    }

    /**
     * @inheritdoc
     */
    public function getMethod() : string
    {
        return $this->method;
    }

    /**
     * @inheritdoc
     */
    public function getProperties() : IDictionary
    {
        return $this->properties;
    }

    /**
     * @inheritdoc
     */
    public function getProtocolVersion() : string
    {
        return $this->protocolVersion;
    }

    /**
     * @inheritdoc
     */
    public function getUri() : Uri
    {
        return $this->uri;
    }

    /**
     * @inheritdoc
     */
    public function setBody(IHttpBody $body) : void
    {
        $this->body = $body;
    }

    /**
     * @inheritdoc
     */
    protected function setMethod(string $method) : void
    {
        $uppercaseMethod = strtoupper($method);

        if (!in_array($uppercaseMethod, self::$validMethod)) {
            throw new InvalidArgumentException("Invalid HTTP method $method");
        }

        $this->method = $method;
    }
}
