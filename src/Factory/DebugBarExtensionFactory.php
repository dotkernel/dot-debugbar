<?php

declare(strict_types = 1);

namespace Dot\DebugBar\Factory;

use Dot\DebugBar\DebugBar;
use Dot\DebugBar\Extension\DebugBarExtension;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DebugBarExtensionFactory
{
    /**
     * @param ContainerInterface $container
     * @return DebugBarExtension
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): DebugBarExtension
    {
        return new DebugBarExtension(
            $container->get(DebugBar::class),
            $container->get('config')
        );
    }
}
