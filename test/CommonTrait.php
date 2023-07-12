<?php

declare(strict_types=1);

namespace DotTest\DebugBar;

use Dot\DebugBar\DebugBar;

trait CommonTrait
{
    protected array $config = [
        DebugBar::class => [
            'enabled'             => true,
            'ipv4Whitelist'       => [],
            'ipv6Whitelist'       => [],
            'javascript_renderer' => [],
            'application'         => [
                'url' => 'https://example.com',
            ],
        ],
    ];
}
