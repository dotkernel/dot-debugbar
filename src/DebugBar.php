<?php

declare(strict_types = 1);

namespace Dot\DebugBar;

use Closure;
use DebugBar\Bridge\DoctrineCollector;
use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DebugBarException;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;
use Throwable;

class DebugBar extends \DebugBar\DebugBar
{
    public const STATUS_ENABLED = 'enabled';
    public const STATUS_DISABLED = 'disabled';

    private array $config;
    private string $status = self::STATUS_DISABLED;
    private MessagesCollector $messagesCollector;
    private TimeDataCollector $timeDataCollector;
    private PhpInfoCollector $phpInfoCollector;
    private RequestDataCollector $requestDataCollector;
    private MemoryCollector $memoryCollector;
    private ExceptionsCollector $exceptionsCollector;
    private ConfigCollector $configCollector;
    private DoctrineCollector $doctrineCollector;

    /**
     * DebugBar constructor
     * @throws DebugBarException
     */
    public function __construct(EntityManager $entityManager, array $config)
    {
        $debugStack = new DebugStack();
        $entityManager->getConnection()->getConfiguration()->setSQLLogger($debugStack);

        $this->addCollector($this->memoryCollector = new MemoryCollector());
        $this->addCollector($this->phpInfoCollector = new PhpInfoCollector());
        $this->addCollector($this->requestDataCollector = new RequestDataCollector());
        $this->addCollector($this->configCollector = new ConfigCollector($config));
        $this->addCollector($this->doctrineCollector = new DoctrineCollector($debugStack));
        $this->addCollector($this->messagesCollector = new MessagesCollector());
        $this->addCollector($this->timeDataCollector = new TimeDataCollector());
        $this->addCollector($this->exceptionsCollector = new ExceptionsCollector());

        $this->config = $config[DebugBar::class] ?? [];

        $this->getJavascriptRenderer()->addAssets(
            [
                'dotkernel.css'
            ], []
        );

        $this->getJavascriptRenderer()->setOptions($this->config['javascript_renderer'] ?? []);
        if (!empty($this->config['javascript_renderer']['disable_jquery'])) {
            $this->getJavascriptRenderer()->disableVendor('jquery');
        }
        if (!empty($this->config['javascript_renderer']['disable_fontawesome'])) {
            $this->getJavascriptRenderer()->disableVendor('fontawesome');
        }
        if (!empty($this->config['javascript_renderer']['disable_highlightjs'])) {
            $this->getJavascriptRenderer()->disableVendor('highlightjs');
        }
    }

    /**
     * @param string $ipAddress
     * @return bool
     */
    public function shouldEnable(string $ipAddress): bool
    {
        /**
         * If config file is missing: DebugBar is disabled
         */
        if (empty($this->config)) {
            return false;
        }

        /**
         * If config.enabled is missing/empty/false: DebugBar is disabled
         */
        if (empty($this->config['enabled'])) {
            return false;
        }

        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $this->shouldEnableIPV4($ipAddress);
        } elseif (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return $this->shouldEnableIPV6($ipAddress);
        } else {
            return false;
        }
    }

    /**
     * @param string $ipAddress
     * @return bool
     */
    private function shouldEnableIPV4(string $ipAddress): bool
    {
        /**
         * If config.ipv4Whitelist is missing/empty: DebugBar is disabled
         */
        if (empty($this->config['ipv4Whitelist'])) {
            return false;
        }

        /**
         * If * is in config.ipv4Whitelist: DebugBar is enabled
         */
        if (in_array('*', $this->config['ipv4Whitelist'])) {
            return true;
        }

        /**
         * If user IP is in config.ipv4Whitelist: DebugBar is enabled
         */
        return in_array($ipAddress, $this->config['ipv4Whitelist']);
    }

    /**
     * @param string $ipAddress
     * @return bool
     */
    private function shouldEnableIPV6(string $ipAddress): bool
    {
        /**
         * If config.ipv6Whitelist is missing/empty: DebugBar is disabled
         */
        if (empty($this->config['ipv6Whitelist'])) {
            return false;
        }

        /**
         * If * is in config.ipv6Whitelist: DebugBar is enabled
         */
        if (in_array('*', $this->config['ipv6Whitelist'])) {
            return true;
        }

        /**
         * If user IP is in config.ipv6Whitelist: DebugBar is enabled
         */
        return in_array($ipAddress, $this->config['ipv6Whitelist']);
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->status === self::STATUS_ENABLED;
    }

    /**
     * @return $this
     */
    public function enable(): self
    {
        $this->status = self::STATUS_ENABLED;
        return $this;
    }

    /**
     * @return $this
     */
    public function disable(): self
    {
        $this->status = self::STATUS_DISABLED;
        return $this;
    }

    /**
     * @param $message
     * @param string $label
     * @param bool $isString
     * @return $this
     */
    public function addMessage($message, string $label = 'info', bool $isString = true): self
    {
        $this->messagesCollector->addMessage($message, $label, $isString);
        return $this;
    }

    /**
     * @param string $name
     * @param string|null $label
     * @return $this
     */
    public function startTimer(string $name, ?string $label = null): self
    {
        $this->timeDataCollector->startMeasure($name, $label);
        return $this;
    }

    /**
     * @param string $name
     * @param array $params
     * @return $this
     * @throws DebugBarException
     */
    public function stopTimer(string $name, array $params = []): self
    {
        $this->timeDataCollector->stopMeasure($name, $params);
        return $this;
    }

    /**
     * @param string $name
     * @param Closure $closure
     * @return $this
     */
    public function measure(string $name, Closure $closure): self
    {
        $this->timeDataCollector->measure($name, $closure);
        return $this;
    }

    /**
     * @param Throwable $throwable
     * @return $this
     */
    public function addThrowable(Throwable $throwable): self
    {
        $this->exceptionsCollector->addThrowable($throwable);
        return $this;
    }
}
