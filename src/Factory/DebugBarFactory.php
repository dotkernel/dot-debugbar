<?php

declare(strict_types=1);

namespace Dot\DebugBar\Factory;

use DebugBar\DebugBarException;
use Doctrine\ORM\EntityManager;
use Dot\DebugBar\DebugBar;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function array_key_exists;
use function is_array;
use function is_bool;

class DebugBarFactory
{
    public const MESSAGE_MISSING_CONFIG                     = 'Unable to find config in the container';
    public const MESSAGE_MISSING_CONFIG_APPLICATION         = 'Missing/invalid config: application';
    public const MESSAGE_MISSING_CONFIG_APPLICATION_URL     = 'Missing/invalid config: application url';
    public const MESSAGE_MISSING_CONFIG_ENABLED             = 'Missing/invalid config: enabled';
    public const MESSAGE_MISSING_CONFIG_IPV4_WHITELIST      = 'Missing/invalid config: ipv4Whitelist';
    public const MESSAGE_MISSING_CONFIG_IPV6_WHITELIST      = 'Missing/invalid config: ipv6Whitelist';
    public const MESSAGE_MISSING_CONFIG_JAVASCRIPT_RENDERER = 'Missing/invalid config: javascript_renderer';
    public const MESSAGE_MISSING_DEBUG_BAR                  = 'Unable to find DebugBar in the container';
    public const MESSAGE_MISSING_ENTITY_MANAGER             = 'Unable to find EntityManager in the container';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DebugBarException
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container): DebugBar
    {
        if (! $container->has(EntityManager::class)) {
            throw new Exception(self::MESSAGE_MISSING_ENTITY_MANAGER);
        }

        if (! $container->has('config')) {
            throw new Exception(self::MESSAGE_MISSING_CONFIG);
        }
        $config = $container->get('config');

        if (! array_key_exists('enabled', $config) || ! is_bool($config['enabled'])) {
            throw new Exception(self::MESSAGE_MISSING_CONFIG_ENABLED);
        }

        if (! array_key_exists('ipv4Whitelist', $config) || ! is_array($config['ipv4Whitelist'])) {
            throw new Exception(self::MESSAGE_MISSING_CONFIG_IPV4_WHITELIST);
        }

        if (! array_key_exists('ipv6Whitelist', $config) || ! is_array($config['ipv6Whitelist'])) {
            throw new Exception(self::MESSAGE_MISSING_CONFIG_IPV6_WHITELIST);
        }

        if (! array_key_exists('javascript_renderer', $config) || ! is_array($config['javascript_renderer'])) {
            throw new Exception(self::MESSAGE_MISSING_CONFIG_JAVASCRIPT_RENDERER);
        }

        /** @var EntityManager $em */
        $em = $container->get(EntityManager::class);
        return new DebugBar(
            $em->getConnection()->getConfiguration(),
            $container->get('config') ?? []
        );
    }
}
