<?php declare(strict_types=1);

namespace Darsyn\Obfuscate;

use Go\Instrument\Transformer\SourceTransformer;
use Go\Instrument\Transformer\StreamMetaData;

class ObfuscateTransformer implements SourceTransformer, ObfuscateInterface
{
    const LINE_ENDING = "\n";
    const LINE_LENGTH = 80;

    /** @var string $key */
    private $key;
    /** @var string $preamble */
    private $preamble;
    /** @var int $preambleLength */
    private $preambleLength;

    public function __construct(string $key, string $preamble)
    {
        $this->key = $key;
        $this->preamble = $preamble;
        $this->preambleLength = strlen($this->preamble);
    }
    
    public function encrypt(string $source): string
    {
        if ($this->isObfuscated($source)) {
            return $source;
        }
        return $this->preamble . chunk_split(
            base64_encode($this->xor($source)),
            static::LINE_LENGTH,
            static::LINE_ENDING
        );
    }
    public function decrypt(string $source): string
    {
        if (!$this->isObfuscated($source)) {
            return $source;
        }
        return $this->xor(base64_decode(
            str_replace(static::LINE_ENDING, '', substr($source, $this->preambleLength))
        ));
    }

    /** @inheritdoc */
    public function transform(StreamMetaData $metadata): string
    {
        return $this->decrypt($metadata->source);
    }
    
    private function isObfuscated(string $source): bool
    {
        return substr($source, 0, $this->preambleLength) === $this->preamble;
    }

    private function xor(string $source): string
    {
        $output = '';
        $keyLength = strlen($this->key);
        $sourceLength = strlen($source);
        for ($i = 0; $i < $sourceLength; $i++) {
            $output .= ($source[$i] ^ $this->key[$i % $keyLength]);
        }
        return $output;
    }
}
