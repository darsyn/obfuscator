<?php declare(strict_types=1);

namespace Darsyn\Obfuscate\Filter;

use Darsyn\Obfuscate\StreamMetaData;
use Darsyn\Obfuscate\Transformer\TransformerInterface;
use php_user_filter as PhpStreamFilter;

class ObfuscateFilter extends PhpStreamFilter implements FilterInterface
{
    /** @var string $filterId */
    private static $filterId;
    /** @var \Darsyn\Obfuscate\Transformer\TransformerInterface $transformer */
    private static $transformer;

    /** @inheritdoc */
    public static function register(
        TransformerInterface $transformer,
        string $filterId = self::FILTER_IDENTIFIER
    ): string {
        if (self::isRegistered()) {
            throw new \RuntimeException(sprintf('Class "%s" already registered as stream filter.', __CLASS__));
        }
        static::$transformer = $transformer;
        $result = stream_filter_register($filterId, __CLASS__);
        if (!$result) {
            throw new \RuntimeException(sprintf('Stream filter "%s" already registered.', $filterId));
        }
        return self::$filterId = $filterId;
    }

    /** @inheritdoc */
    public static function isRegistered(): bool
    {
        return !empty(self::$filterId);
    }

    /** @inheritdoc */
    public static function getRegisteredId(): string
    {
        if (!self::isRegistered()) {
            throw new \RuntimeException('Stream filter was not registered');
        }
        return self::$filterId;
    }

    /** {@inheritdoc} */
    public function filter($in, $out, &$consumed, $closing): int
    {
        $data = '';
        while ($bucket = stream_bucket_make_writeable($in)) {
            $data .= $bucket->data;
        }
        // FYI, $this->stream is a pointer to the source.
        if ($closing || feof($this->stream)) {
            $consumed = strlen($data);
            $metadata = new StreamMetaData($this->stream, $data);
            $bucket = stream_bucket_new($this->stream, self::$transformer->shouldTransform($metadata)
                ? self::$transformer->transform($metadata)
                : $metadata->source
            );
            stream_bucket_append($out, $bucket);
            return PSFS_PASS_ON;
        }
        return PSFS_FEED_ME;
    }
}
