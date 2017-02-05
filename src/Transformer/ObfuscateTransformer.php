<?php declare(strict_types=1);

namespace Darsyn\Obfuscate\Transformer;

use Darsyn\Obfuscate\StreamMetaData;

class ObfuscateTransformer implements TransformerInterface
{
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

    /**
     * @access public
     * @param \Darsyn\Obfuscate\StreamMetaData $metadata
     * @return boolean
     */
    public function shouldTransform(StreamMetaData $metadata): bool
    {
        return substr($metadata->source, 0, $this->preambleLength) === $this->preamble;
    }

    /** @inheritdoc */
    public function transform(StreamMetaData $metadata): string
    {
        if (!$this->shouldTransform($metadata)) {
            return $metadata->source;
        }
        return $this->xor($this->removePreamble($metadata));
    }

    /**
     * @access private
     * @param \Darsyn\Obfuscate\StreamMetaData $metadata
     * @return string
     */
    private function removePreamble(StreamMetaData $metadata): string
    {
        return substr($metadata->source, $this->preambleLength);
    }

    /**
     * @access private
     * @param string $source
     * @return string
     */
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
