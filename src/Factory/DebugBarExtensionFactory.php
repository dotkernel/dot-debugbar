<?php

declare(strict_types=1);

namespace Dot\DebugBar\Factory;

use Dot\DebugBar\DebugBar;
use Dot\DebugBar\DebugBarInterface;
use Dot\DebugBar\Extension\DebugBarExtension;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function array_key_exists;
use function is_array;
use function is_string;

class DebugBarExtensionFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container): DebugBarExtension
    {
        if (! $container->has(DebugBarInterface::class)) {
            throw new Exception(DebugBarFactory::MESSAGE_MISSING_DEBUG_BAR);
        }

        if (! $container->has('config')) {
            throw new Exception(DebugBarFactory::MESSAGE_MISSING_CONFIG);
        }
        $config = $container->get('config');

        if (
            ! array_key_exists(DebugBar::class, $config)
            || ! is_array($config[DebugBar::class])
            || empty($config[DebugBar::class])
        ) {
            throw new Exception(DebugBarFactory::MESSAGE_MISSING_PACKAGE_CONFIG);
        }

        if (
            ! array_key_exists('application', $config)
            || ! is_array($config['application'])
            || empty($config['application'])
        ) {
            throw new Exception(DebugBarFactory::MESSAGE_MISSING_CONFIG_APPLICATION);
        }

        if (
            ! array_key_exists('url', $config['application'])
            || ! is_string($config['application']['url'])
            || empty($config['application']['url'])
        ) {
            throw new Exception(DebugBarFactory::MESSAGE_MISSING_CONFIG_APPLICATION_URL);
        }

        return new DebugBarExtension(
            $container->get(DebugBarInterface::class),
            $config['application']['url']
        );
    }
}
