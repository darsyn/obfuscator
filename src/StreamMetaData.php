<?php declare(strict_types=1);

/**
 * This file was taken from the Go! AOP framework by Alexander Lisachenko
 * <lisachenko.it@gmail.com>. This source file is subject to the MIT license.
 */

namespace Darsyn\Obfuscate;

class StreamMetaData
{
    /** @var array $propertyMap */
    private static $propertyMap = [
        // True is the stream timed out while waiting for data on the last call
        // to fread() or fgets().
        'timed_out' => 'isTimedOut',
        // True if the stream has reached end-of-file.
        'blocked' => 'isBlocked',
        // True if the stream has reached end-of-file.
        'eof' => 'isEOF',
        // The number of bytes currently contained in PHP's own internal buffer.
        'unread_bytes' => 'unreadBytesCount',
        // A label describing the underlying implementation of the stream.
        'stream_type' => 'streamType',
        // A label describing the protocol wrapper implementation layered over
        // the stream.
        'wrapper_type' => 'wrapperType',
        // Wrapper-specific data attached to this stream.
        'wrapper_data' => 'wrapperData',
        // Array containing the names of any filters that have been stacked onto
        // this stream.
        'filters' => 'filterList',
        // The type of access required for this stream.
        'mode' => 'mode',
        // Whether the current stream can be seeked.
        'seekable' => 'isSeekable',
        // The URI/filename associated with this stream.
        'uri' => 'uri',
        // The contents of the stream.
        'source' => 'source',
    ];
    /** @var array $properties */
    private $properties = [];

    /**
     * Creates metadata object from stream
     *
     * @param resource $stream Instance of stream
     * @param string $source Source code or null
     * @throws \InvalidArgumentException
     */
    public function __construct(resource $stream, string $source = null)
    {
        if (!is_resource($stream)) {
            throw new \InvalidArgumentException('Stream should be valid resource.');
        }
        $metadata = stream_get_meta_data($stream);
        $this->properties['source'] = $source;
        if (preg_match('/resource=(.+)$/', $metadata['uri'], $matches)) {
            $metadata['uri'] = $this->realpath($matches[1]);
        }
        foreach ($metadata as $key => $value) {
            if (!isset(self::$propertyMap[$key])) {
                continue;
            }
            $mappedKey = self::$propertyMap[$key];
            $this->properties[$mappedKey] = $value;
        }
    }

    /**
     * Get Property from Stream Metadata
     *
     * @access public
     * @param string $property
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function __get(string $property)
    {
        if (isset($this->properties[$property])) {
            return $this->properties[$property];
        }
        throw new \InvalidArgumentException(sprintf(
            'Property "%s" does not exist in stream metadata.',
            $property
        ));
    }

    /**
     * Custom replacement for realpath() and stream_resolve_include_path()
     *
     * @param string|array $somePath Path without normalization or array of paths
     * @param bool $shouldCheckExistence Flag for checking existence of resolved filename
     *
     * @return array|bool|string
     */
    private function realpath($somePath, $shouldCheckExistence = false)
    {
        // Do not resolve empty string/false/arrays into the current path
        if (!$somePath) {
            return $somePath;
        }
        if (is_array($somePath)) {
            return array_map(array(__CLASS__, __FUNCTION__), $somePath);
        }
        // Trick to get scheme name and path in one action. If no scheme, then there will be only one part
        $components = explode('://', $somePath, 2);
        list ($pathScheme, $path) = isset($components[1]) ? $components : array(null, $components[0]);
        // Optimization to bypass complex logic for simple paths (eg. not in phar archives)
        if (!$pathScheme && ($fastPath = stream_resolve_include_path($somePath))) {
            return $fastPath;
        }
        $isRelative = !$pathScheme && ($path[0] !== '/') && ($path[1] !== ':');
        if ($isRelative) {
            $path = getcwd() . DIRECTORY_SEPARATOR . $path;
        }
        // resolve path parts (single dot, double dot and double delimiters)
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        if (strpos($path, '.') !== false) {
            $parts     = explode(DIRECTORY_SEPARATOR, $path);
            $absolutes = [];
            foreach ($parts as $part) {
                if ('.' == $part) {
                    continue;
                } elseif ('..' == $part) {
                    array_pop($absolutes);
                } else {
                    $absolutes[] = $part;
                }
            }
            $path = implode(DIRECTORY_SEPARATOR, $absolutes);
        }
        if ($pathScheme) {
            $path = "{$pathScheme}://{$path}";
        }
        if ($shouldCheckExistence && !file_exists($path)) {
            return false;
        }
        return $path;
    }
}
