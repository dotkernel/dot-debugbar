<?php

declare(strict_types = 1);

namespace Dot\DebugBar\Factory;

use DebugBar\DebugBarException;
use Doctrine\ORM\EntityManager;
use Dot\DebugBar\DebugBar;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DebugBarFactory
{
    /**
     * @param ContainerInterface $container
     * @return DebugBar
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DebugBarException
     */
    public function __invoke(ContainerInterface $container): DebugBar
    {
        return new DebugBar(
            $container->get(EntityManager::class),
            $container->get('config') ?? []
        );
    }
}
