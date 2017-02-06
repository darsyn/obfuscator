<?php declare(strict_types=1);

namespace Darsyn\Obfuscate;

use Go\Core\AspectContainer;
use Go\Core\AspectKernel;
use Go\Instrument\Transformer\CachingTransformer;
use Go\Instrument\Transformer\SourceTransformer;

class Kernel extends AspectKernel
{
    const ENABLE_AOP = 'enableAopTransformers';

    /** @var \Go\Instrument\Transformer\SourceTransformer $obfuscateTransformer */
    private $obfuscateTransformer;

    public function __construct(SourceTransformer $transformer)
    {
        $this->obfuscateTransformer = $transformer;
        static::$instance = $this;
    }

    protected function configureAop(AspectContainer $container)
    {
    }

    public function registerTransformers(): array
    {
        $cachedObfuscateTransformers = [new CachingTransformer($this, function () {
            return [$this->obfuscateTransformer];
        }, $this->getContainer()->get('aspect.cache.path.manager'))];

        $kernelOptions = $this->getContainer()->get('kernel.options');
        if (isset($kernelOptions[static::ENABLE_AOP]) && $kernelOptions[static::ENABLE_AOP]) {
            return array_merge($cachedObfuscateTransformers, parent::registerTransformers());
        }

        return $cachedObfuscateTransformers;
    }
}
