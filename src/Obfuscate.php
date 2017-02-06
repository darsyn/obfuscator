<?php declare(strict_types=1);

namespace Darsyn\Obfuscate;

use Go\Instrument\Transformer\SourceTransformer;

class Obfuscate
{
    const DEFAULT_KEY = 'This is not a secure key and is not meant to encrypt the source code.';
    const DEFAULT_PREAMBLE = "<?php exit('Protected by Darsyn Obfuscator.'); ?>\n\n";

    public function __construct(array $options = [], SourceTransformer $transformer = null)
    {
        $transformer = $transformer ?: new ObfuscateTransformer(
            static::DEFAULT_KEY,
            static::DEFAULT_PREAMBLE
        );
        $kernel = new Kernel($transformer);
        $kernel->init(array_merge([
            'debug' => true,
            'cacheDir' => null,
            'includePaths' => [],
            Kernel::ENABLE_AOP => false,
        ], $options));
    }
}
