<?php

namespace Frenet\Service\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Minimal immutable PSR-7 response implementation, used to remove the
 * dependency on guzzlehttp/psr7 (unmaintained since its 1.x line and the
 * source of several of the CVEs this client replaces).
 *
 * @package Frenet\Service\Http
 */
class Response implements ResponseInterface
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var string
     */
    private $reasonPhrase;

    /**
     * @var string
     */
    private $protocolVersion = '1.1';

    /**
     * @var array Normalized (lowercase) header name => original case name
     */
    private $headerNames = [];

    /**
     * @var array Normalized (lowercase) header name => array of values
     */
    private $headers = [];

    /**
     * @var StreamInterface
     */
    private $body;

    /**
     * @param int                      $statusCode
     * @param array                    $headers
     * @param string|StreamInterface   $body
     * @param string                   $reasonPhrase
     */
    public function __construct($statusCode = 200, array $headers = [], $body = '', $reasonPhrase = '')
    {
        $this->statusCode = (int) $statusCode;
        $this->reasonPhrase = (string) $reasonPhrase;
        $this->body = $body instanceof StreamInterface ? $body : new Stream((string) $body);

        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $new = clone $this;
        $new->statusCode = (int) $code;
        $new->reasonPhrase = (string) $reasonPhrase;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version)
    {
        $new = clone $this;
        $new->protocolVersion = $version;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        $result = [];

        foreach ($this->headers as $normalized => $values) {
            $result[$this->headerNames[$normalized]] = $values;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name)
    {
        return isset($this->headers[strtolower($name)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name)
    {
        $normalized = strtolower($name);

        return isset($this->headers[$normalized]) ? $this->headers[$normalized] : [];
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name)
    {
        return implode(', ', $this->getHeader($name));
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value)
    {
        $new = clone $this;
        $new->setHeader($name, $value);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value)
    {
        $new = clone $this;
        $normalized = strtolower($name);
        $values = is_array($value) ? array_values($value) : [$value];

        if (isset($new->headers[$normalized])) {
            $new->headers[$normalized] = array_merge($new->headers[$normalized], $values);
        } else {
            $new->headerNames[$normalized] = $name;
            $new->headers[$normalized] = $values;
        }

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name)
    {
        $new = clone $this;
        $normalized = strtolower($name);
        unset($new->headers[$normalized], $new->headerNames[$normalized]);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {
        $new = clone $this;
        $new->body = $body;

        return $new;
    }

    /**
     * @param string       $name
     * @param string|array $value
     */
    private function setHeader($name, $value)
    {
        $normalized = strtolower($name);
        $this->headerNames[$normalized] = $name;
        $this->headers[$normalized] = is_array($value) ? array_values($value) : [$value];
    }
}
