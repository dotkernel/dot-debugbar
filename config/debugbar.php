<?php

declare(strict_types=1);

use Dot\DebugBar\DebugBar;

return [
    DebugBar::class => [
        /**
         * Enable DebugBar by whitelisting your IP address
         */
        'ipWhitelist' => [
            '127.0.0.1'
        ],
        /**
         * @see \DebugBar\JavascriptRenderer::setOptions()
         */
        'javascript_renderer' => [
            'base_url' => '',
            'base_path' => '',
            'disable_jquery' => true,
            'disable_fontawesome' => true,
            'disable_highlightjs' => false,
        ]
    ]
];
