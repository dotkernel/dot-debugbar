<?php

declare(strict_types=1);

namespace DotTest\DebugBar;

trait CommonTrait
{
    protected array $config = [
        'enabled'             => true,
        'ipv4Whitelist'       => [],
        'ipv6Whitelist'       => [],
        'javascript_renderer' => [],
        'application'         => [
            'url' => 'https://example.com',
        ],
    ];
}
