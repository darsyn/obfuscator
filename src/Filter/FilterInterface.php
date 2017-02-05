<?php declare(strict_types=1);

namespace Darsyn\Obfuscate\Filter;

use Darsyn\Obfuscate\Transformer\TransformerInterface;

interface FilterInterface
{
    const FILTER_IDENTIFIER = 'darsyn.obfuscate';
    const PHP_FILTER_READ = 'php://filter/read=';

    /**
     * Register this class as a stream filter to PHP.
     *
     * @static
     * @access public
     * @param \Darsyn\Obfuscate\Transformer\TransformerInterface $transformer
     * @param string $filterId
     * @throws \RuntimeException
     * @return string
     */
    public static function register(
        TransformerInterface $transformer,
        string $filterId = self::FILTER_IDENTIFIER
    ): string;

    /**
     * Is the current class registered as a stream filter?
     *
     * @static
     * @access public
     * @return boolean
     */
    public static function isRegistered(): bool;

    /**
     * Returns the name of registered filter
     *
     * @static
     * @access public
     * @throws \RuntimeException if filter was not registered
     * @return string
     */
    public static function getRegisteredId(): string;
}