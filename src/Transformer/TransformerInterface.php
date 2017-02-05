<?php declare(strict_types=1);

namespace Darsyn\Obfuscate\Transformer;

use Darsyn\Obfuscate\StreamMetaData;

interface TransformerInterface
{
    /**
     * @access public
     * @param \Darsyn\Obfuscate\StreamMetaData $metadata
     * @return boolean
     */
    public function shouldTransform(StreamMetaData $metadata): bool;

    /**
     * Transform Source Code
     *
     * @access public
     * @param \Darsyn\Obfuscate\StreamMetaData $metadata
     * @return string
     */
    public function transform(StreamMetaData $metadata): string;
}