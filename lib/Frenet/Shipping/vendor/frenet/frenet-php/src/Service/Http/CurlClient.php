<?php

namespace Frenet\Service\Http;

/**
 * Minimal cURL-based HTTP client, replacing guzzlehttp/guzzle.
 *
 * Only implements what Frenet\Service\Connection::makeRequest() actually
 * uses: a single "request($method, $uri, $options)" call with a JSON body
 * and custom headers, mirroring Guzzle's default behavior of throwing on
 * connection failures and on 4xx/5xx responses.
 *
 * @package Frenet\Service\Http
 */
class CurlClient
{
    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return Response
     * @throws \RuntimeException
     */
    public function request($method, $uri, array $options = [])
    {
        $curlHeaders = [];

        $body = null;
        if (isset($options['json'])) {
            $body = json_encode($options['json']);
            $curlHeaders[] = 'Content-Type: application/json';
        }

        if (isset($options['headers']) && is_array($options['headers'])) {
            foreach ($options['headers'] as $name => $value) {
                $curlHeaders[] = $name . ': ' . $value;
            }
        }

        $method = strtoupper($method);
        $handle = curl_init();

        curl_setopt_array($handle, [
            CURLOPT_URL => $uri,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $curlHeaders,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
        ]);

        if ($body !== null && $method !== 'GET') {
            curl_setopt($handle, CURLOPT_POSTFIELDS, $body);
        }

        $raw = curl_exec($handle);

        if ($raw === false) {
            $error = curl_error($handle);
            $errno = curl_errno($handle);
            curl_close($handle);

            throw new \RuntimeException(
                sprintf('HTTP request to "%s" failed: %s', $uri, $error),
                $errno
            );
        }

        $statusCode = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        curl_close($handle);

        $rawHeaders = substr($raw, 0, $headerSize);
        $responseBody = substr($raw, $headerSize);

        $response = new Response($statusCode, $this->parseHeaders($rawHeaders), $responseBody);

        // Redirects (3xx) are treated as failures rather than followed: this client never
        // sets CURLOPT_FOLLOWLOCATION, so a 3xx here means the body is a redirect page, not
        // the expected JSON payload, and must not be handed to callers as a "success".
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new \RuntimeException(
                sprintf('HTTP request to "%s" returned status code %d: %s', $uri, $statusCode, $responseBody),
                $statusCode
            );
        }

        return $response;
    }

    /**
     * @param string $rawHeaders
     *
     * @return array
     */
    private function parseHeaders($rawHeaders)
    {
        $headers = [];

        // With redirects disabled there is a single header block, one line per header.
        $lines = explode("\r\n", trim($rawHeaders));

        foreach ($lines as $line) {
            if (strpos($line, ':') === false) {
                continue;
            }

            list($name, $value) = explode(':', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (isset($headers[$name])) {
                $headers[$name][] = $value;
            } else {
                $headers[$name] = [$value];
            }
        }

        return $headers;
    }
}
