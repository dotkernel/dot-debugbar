<?php

declare(strict_types=1);

return [
    Dot\DebugBar\DebugBar::class => [
        /**
         * Enable/Disable DebugBar
         */
        'enabled' => true,

        /**
         * Enable DebugBar by whitelisting your IPV4 address
         */
        'ipv4Whitelist' => [
            '127.0.0.1',
        ],

        /**
         * Enable DebugBar by whitelisting your IPV6 address
         */
        'ipv6Whitelist' => [
            '::1',
        ],

        /**
         * @see \DebugBar\JavascriptRenderer::setOptions()
         */
        'javascript_renderer' => [
            'base_url'            => '',
            'base_path'           => '',
            'disable_jquery'      => false,
            'disable_fontawesome' => false,
            'disable_highlightjs' => false,
        ],
    ],
];
