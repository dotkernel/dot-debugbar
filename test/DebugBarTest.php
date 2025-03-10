<?php

declare(strict_types=1);

namespace DotTest\DebugBar;

use DebugBar\DebugBarException;
use Doctrine\DBAL\Configuration;
use Dot\DebugBar\DebugBar;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class DebugBarTest extends TestCase
{
    use CommonTrait;

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testCanCreate(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $dotDebugBar   = new DebugBar($configuration, $this->config);
        $this->assertSame(DebugBar::class, $dotDebugBar::class);
    }

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testWillNotEnableIfConfigDisabled(): void
    {
        $config                             = $this->config;
        $config[DebugBar::class]['enabled'] = false;
        $configuration                      = $this->createMock(Configuration::class);
        $dotDebugBar                        = new DebugBar($configuration, $config);
        $this->assertFalse($dotDebugBar->shouldEnable(''));
    }

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testWillNotEnableIfConfigEnabledButIpAddressIsInvalid(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $dotDebugBar   = new DebugBar($configuration, $this->config);
        $this->assertFalse($dotDebugBar->shouldEnable('test'));
    }

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testWillNotEnableIfConfigEnabledAndValidIpv4AddressNotWhitelisted(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $dotDebugBar   = new DebugBar($configuration, $this->config);
        $this->assertFalse($dotDebugBar->shouldEnable('127.0.0.1'));
    }

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testWillNotEnableIfConfigEnabledAndValidIpv6AddressNotWhitelisted(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $dotDebugBar   = new DebugBar($configuration, $this->config);
        $this->assertFalse(
            $dotDebugBar->shouldEnable('::1')
        );
    }

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testWillEnableIfConfigEnabledAndValidWhitelistedIpv4Address(): void
    {
        $config                                     = $this->config;
        $config[DebugBar::class]['ipv4Whitelist'][] = '127.0.0.1';
        $configuration                              = $this->createMock(Configuration::class);
        $dotDebugBar                                = new DebugBar($configuration, $config);
        $this->assertTrue($dotDebugBar->shouldEnable('127.0.0.1'));
    }

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testWillEnableIfConfigEnabledAndValidWhitelistedIpv6Address(): void
    {
        $config                                     = $this->config;
        $config[DebugBar::class]['ipv6Whitelist'][] = '::1';
        $configuration                              = $this->createMock(Configuration::class);
        $dotDebugBar                                = new DebugBar($configuration, $config);
        $this->assertTrue($dotDebugBar->shouldEnable('::1'));
    }

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testWillEnableIfConfigEnabledAndAllowAnyValidIpv4Address(): void
    {
        $config                                     = $this->config;
        $config[DebugBar::class]['ipv4Whitelist'][] = '*';
        $configuration                              = $this->createMock(Configuration::class);
        $dotDebugBar                                = new DebugBar($configuration, $config);
        $this->assertTrue($dotDebugBar->shouldEnable('127.0.0.1'));
    }

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testWillEnableIfConfigEnabledAndAllowAnyValidIpv6Address(): void
    {
        $config                                     = $this->config;
        $config[DebugBar::class]['ipv6Whitelist'][] = '*';
        $configuration                              = $this->createMock(Configuration::class);
        $dotDebugBar                                = new DebugBar($configuration, $config);
        $this->assertTrue($dotDebugBar->shouldEnable('::1'));
    }

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testWillToggle(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $dotDebugBar   = new DebugBar($configuration, $this->config);
        $this->assertFalse($dotDebugBar->isEnabled());
        $dotDebugBar->enable();
        $this->assertSame(DebugBar::class, $dotDebugBar::class);
        $this->assertTrue($dotDebugBar->isEnabled());
        $dotDebugBar->disable();
        $this->assertSame(DebugBar::class, $dotDebugBar::class);
        $this->assertFalse($dotDebugBar->isEnabled());
    }

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testWillAddMessage(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $dotDebugBar   = new DebugBar($configuration, $this->config);
        $this->assertCount(0, $dotDebugBar->getMessagesCollector()->getMessages());
        $dotDebugBar->addMessage('test');
        $this->assertSame(DebugBar::class, $dotDebugBar::class);
        $this->assertCount(1, $dotDebugBar->getMessagesCollector()->getMessages());
    }

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testWillStartStopMeasureTimer(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $dotDebugBar   = new DebugBar($configuration, $this->config);
        $this->assertFalse($dotDebugBar->getTimeDataCollector()->hasStartedMeasure('test'));
        $dotDebugBar->startTimer('test');
        $this->assertSame(DebugBar::class, $dotDebugBar::class);
        $this->assertTrue($dotDebugBar->getTimeDataCollector()->hasStartedMeasure('test'));
        $dotDebugBar->stopTimer('test');
        $this->assertSame(DebugBar::class, $dotDebugBar::class);
        $this->assertFalse($dotDebugBar->getTimeDataCollector()->hasStartedMeasure('test'));
    }

    /**
     * @throws DebugBarException
     * @throws Exception
     */
    public function testWillAddThrowable(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $dotDebugBar   = new DebugBar($configuration, $this->config);
        $this->assertCount(0, $dotDebugBar->getExceptionsCollector()->getExceptions());
        $dotDebugBar->addThrowable(new \Exception('test'));
        $this->assertSame(DebugBar::class, $dotDebugBar::class);
        $this->assertCount(1, $dotDebugBar->getExceptionsCollector()->getExceptions());
    }
}
