<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/net/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Aphiria\Net\Formatting;

use Aphiria\Net\Uri;
use Opulence\Collections\IImmutableDictionary;
use Opulence\Collections\ImmutableHashTable;
use Opulence\Collections\KeyValuePair;

/**
 * Defines the URI parser
 */
class UriParser
{
    /** @var array The mapping of raw query strings to their parsed collections */
    private $parsedQueryStringCache = [];

    /**
     * Parses a URI's query string into a collection
     *
     * @param Uri $uri The URI to parse
     * @return IImmutableDictionary The parsed query string
     */
    public function parseQueryString(Uri $uri): IImmutableDictionary
    {
        if (($queryString = $uri->getQueryString()) === null) {
            return new ImmutableHashTable([]);
        }

        if (!isset($this->parsedQueryStringCache[$queryString])) {
            $parsedQueryString = [];
            parse_str($queryString, $parsedQueryString);
            $kvps = [];

            foreach ($parsedQueryString as $key => $value) {
                $kvps[] = new KeyValuePair($key, $value);
            }

            $this->parsedQueryStringCache[$queryString] = new ImmutableHashTable($kvps);
        }

        return $this->parsedQueryStringCache[$queryString];
    }
}
