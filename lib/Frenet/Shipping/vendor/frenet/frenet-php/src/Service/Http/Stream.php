<?php

namespace Frenet\Service\Http;

use Psr\Http\Message\StreamInterface;

/**
 * Minimal in-memory PSR-7 stream, backed by a plain string.
 *
 * @package Frenet\Service\Http
 */
class Stream implements StreamInterface
{
    /**
     * @var string
     */
    private $contents;

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @param string $contents
     */
    public function __construct($contents = '')
    {
        $this->contents = (string) $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->contents;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return strlen($this->contents);
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return $this->position >= strlen($this->contents);
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $length = strlen($this->contents);

        switch ($whence) {
            case SEEK_CUR:
                $this->position += $offset;
                break;
            case SEEK_END:
                $this->position = $length + $offset;
                break;
            case SEEK_SET:
            default:
                $this->position = $offset;
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        throw new \RuntimeException('This stream is read-only.');
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        $data = substr($this->contents, $this->position, $length);
        $this->position += strlen($data);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        $remainder = substr($this->contents, $this->position);
        $this->position = strlen($this->contents);

        return $remainder;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        return $key === null ? [] : null;
    }
}
