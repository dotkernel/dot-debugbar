<?php

declare(strict_types = 1);

namespace Dot\DebugBar\Extension;

use Dot\DebugBar\DebugBar;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use function rtrim;
use function sprintf;

class DebugBarExtension extends AbstractExtension
{
    private ?string $baseUrl;
    private array $config;
    private DebugBar $debugBar;

    /**
     * @param DebugBar $debugBar
     * @param array $config
     */
    public function __construct(DebugBar $debugBar, array $config)
    {
        $this->config = $config;
        $this->debugBar = $debugBar;
        $this->baseUrl = rtrim($this->config['application']['url'], '/');
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('debugBarEnabled', [$this, 'renderDebugBarEnabled']),
            new TwigFunction('debugBarCss', [$this, 'renderDebugBarCss']),
            new TwigFunction('debugBarJs', [$this, 'renderDebugBarJs']),
        ];
    }

    /**
     * @return bool
     */
    public function renderDebugBarEnabled(): bool
    {
        return $this->debugBar->isEnabled();
    }

    /**
     * @return string|null
     */
    public function renderDebugBarCss(): ?string
    {
        if (!$this->debugBar->isEnabled()) {
            return null;
        }

        $return = '';
        $assets = $this->debugBar->getJavascriptRenderer()->getAssets()[0] ?? [];
        foreach ($assets as $asset) {
            $return .= sprintf('<link href="%s/debugbar/%s" rel="stylesheet" />', $this->baseUrl, $asset) . PHP_EOL;
        }

        return $return;
    }

    /**
     * @return string|null
     */
    public function renderDebugBarJs(): ?string
    {
        if (!$this->debugBar->isEnabled()) {
            return null;
        }

        $return = '';
        $assets = $this->debugBar->getJavascriptRenderer()->getAssets()[1] ?? [];
        foreach ($assets as $asset) {
            $return .= sprintf('<script src="%s/debugbar/%s"></script>', $this->baseUrl, $asset) . PHP_EOL;
        }
        $return .= $this->debugBar->getJavascriptRenderer()->render();

        return $return;
    }
}
