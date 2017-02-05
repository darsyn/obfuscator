<?php declare(strict_types=1);

namespace Darsyn\Obfuscate;

use Darsyn\Obfuscate\Filter\ObfuscateFilter;
use Darsyn\Obfuscate\Transformer\ObfuscateTransformer;
use Darsyn\Obfuscate\Transformer\TransformerInterface;

class Obfuscate
{
    const DEFAULT_KEY = 'This is not a secure key and is not meant to encrypt the source code.';
    const DEFAULT_PREAMBLE = '<?php exit("Protected by Darsyn Obfuscator."); ?>';

    public function __construct(TransformerInterface $transformer = null)
    {
        $transformer = $transformer ?: new ObfuscateTransformer(static::KEY, static::DEFAULT_PREAMBLE);
        ObfuscateFilter::register($transformer);
    }
}