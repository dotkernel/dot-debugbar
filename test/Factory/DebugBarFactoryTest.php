<?php

declare(strict_types=1);

namespace DotTest\DebugBar\Factory;

use DebugBar\DebugBarException;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Dot\DebugBar\DebugBar;
use Dot\DebugBar\DebugBarInterface;
use Dot\DebugBar\Factory\DebugBarFactory;
use DotTest\DebugBar\CommonTrait;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DebugBarFactoryTest extends TestCase
{
    use CommonTrait;

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DebugBarException
     */
    public function testWillNotCreateServiceWithoutEntityManager(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('has')
            ->with(EntityManager::class)
            ->willReturn(false);

        $this->expectExceptionMessage(DebugBarFactory::MESSAGE_MISSING_ENTITY_MANAGER);
        (new DebugBarFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DebugBarException
     */
    public function testWillNotCreateServiceWithoutConfig(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method('has')->willReturnMap([
            [EntityManager::class, true],
            ['config', false],
        ]);

        $this->expectExceptionMessage(DebugBarFactory::MESSAGE_MISSING_CONFIG);
        (new DebugBarFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DebugBarException
     */
    public function testWillNotCreateServiceWithoutMissingConfigEnabled(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method('has')->willReturnMap([
            [EntityManager::class, true],
            ['config', true],
        ]);

        $container->method('get')->willReturnMap([
            [EntityManager::class, true],
            [
                'config',
                [
                    DebugBar::class => [
                        'test',
                    ],
                ],
            ],
        ]);

        $this->expectExceptionMessage(DebugBarFactory::MESSAGE_MISSING_CONFIG_ENABLED);
        (new DebugBarFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DebugBarException
     */
    public function testWillNotCreateServiceWithoutMissingConfigIPv4Whitelist(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method('has')->willReturnMap([
            [EntityManager::class, true],
            ['config', true],
        ]);

        $container->method('get')->willReturnMap([
            [EntityManager::class, true],
            [
                'config',
                [
                    DebugBar::class => [
                        'enabled' => true,
                    ],
                ],
            ],
        ]);

        $this->expectExceptionMessage(DebugBarFactory::MESSAGE_MISSING_CONFIG_IPV4_WHITELIST);
        (new DebugBarFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DebugBarException
     */
    public function testWillNotCreateServiceWithoutMissingConfigIPv6Whitelist(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method('has')->willReturnMap([
            [EntityManager::class, true],
            ['config', true],
        ]);

        $container->method('get')->willReturnMap([
            [EntityManager::class, true],
            [
                'config',
                [
                    DebugBar::class => [
                        'enabled'       => true,
                        'ipv4Whitelist' => [],
                    ],
                ],
            ],
        ]);

        $this->expectExceptionMessage(DebugBarFactory::MESSAGE_MISSING_CONFIG_IPV6_WHITELIST);
        (new DebugBarFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DebugBarException
     */
    public function testWillNotCreateServiceWithoutMissingConfigJavaScriptRenderer(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method('has')->willReturnMap([
            [EntityManager::class, true],
            ['config', true],
        ]);

        $container->method('get')->willReturnMap([
            [EntityManager::class, true],
            [
                'config',
                [
                    DebugBar::class => [
                        'enabled'       => true,
                        'ipv4Whitelist' => [],
                        'ipv6Whitelist' => [],
                    ],
                ],
            ],
        ]);

        $this->expectExceptionMessage(DebugBarFactory::MESSAGE_MISSING_CONFIG_JAVASCRIPT_RENDERER);
        (new DebugBarFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DebugBarException
     */
    public function testWillCreateService(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $connection    = $this->createMock(Connection::class);
        $container     = $this->createMock(ContainerInterface::class);
        $em            = $this->createMock(EntityManager::class);

        $container->method('has')->willReturnMap([
            [EntityManager::class, true],
            ['config', true],
        ]);

        $container->method('get')->willReturnMap([
            [EntityManager::class, $em],
            ['config', $this->config],
        ]);

        $em->expects($this->once())->method('getConnection')->willReturn($connection);
        $connection->expects($this->once())->method('getConfiguration')->willReturn($configuration);

        $service = (new DebugBarFactory())($container);
        $this->assertInstanceOf(DebugBarInterface::class, $service);
    }
}
