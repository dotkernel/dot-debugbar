<?php

declare(strict_types=1);

namespace Dot\DebugBar\Extension;

use Dot\DebugBar\DebugBarInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use function sprintf;

use const PHP_EOL;

class DebugBarExtension extends AbstractExtension
{
    private ?string $baseUrl;
    private DebugBarInterface $debugBar;

    public function __construct(DebugBarInterface $debugBar, string $baseUrl)
    {
        $this->debugBar = $debugBar;
        $this->baseUrl  = $baseUrl;
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

    public function renderDebugBarEnabled(): bool
    {
        return $this->debugBar->isEnabled();
    }

    public function renderDebugBarCss(): ?string
    {
        if (! $this->debugBar->isEnabled()) {
            return null;
        }

        $return = '';
        $assets = $this->debugBar->getJavascriptRenderer()->getAssets()[0] ?? [];
        foreach ($assets as $asset) {
            $return .= sprintf('<link href="%s/debugbar/%s" rel="stylesheet" />', $this->baseUrl, $asset) . PHP_EOL;
        }

        return $return;
    }

    public function renderDebugBarJs(): ?string
    {
        if (! $this->debugBar->isEnabled()) {
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
