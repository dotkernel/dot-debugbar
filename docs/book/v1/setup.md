# Setup

Once installed, the following components need to be registered by adding:

* `$app->pipe(\Dot\DebugBar\Middleware\DebugBarMiddleware::class);` to `config/pipeline.php` (preferably after `ServerUrlMiddleware::class`)
* `\Dot\DebugBar\ConfigProvider::class,` to `config/config.php` (preferably at the beginning of the section where the `DotKernel packages` are loaded)
* `\Dot\DebugBar\Extension\DebugBarExtension::class` to `config/autoload/templates.global.php` (inside the array founder under the key `twig` => `extensions`)

Locate the library's assets directory, called `assets` and copy **its contents** to your application under `public/debugbar` directory.

Locate the library's config file `config/debugbar.local.php` and clone it inside your application as:

* `config/autoload/debugbar.local.php.dist`
* `config/autoload/debugbar.local.php`

By default, dot-debugbar is enabled only on the local environment, by whitelisting your local IP address in the config file, inside the array located under the `ipv4Whitelist` key.

If you need to enable it on other environments as well, just whitelist your public IPV4 address.

It can also be enabled globally, by whitelisting the string `*`.

Finally, if you want to keep the whitelists but disable dot-debugbar, you can set `enabled` to **false**.

Inside the config file, you will find additional configurations under the `javascript_renderer` key.
For more configuration values, follow the link in the related comment block.

At this step, dot-debugbar is not displayed yet. In order to display it, you need to call the following Twig functions from your base layout:

* `{{ debugBarCss()|raw }}` (needs to be placed in the head section of the layout, where the CSS files are included)
* `{{ debugBarJs()|raw }}` (needs to be placed in the footer of the layout, where the JS files are included)

If you plan to enable dot-debugbar on production, make sure you clear the relevant cache items by deleting:

* the config cache file: `data/cache/config-cache.php`
* Twig cache directory: `data/cache/twig`

Additionally, you can check if dot-debugbar is enabled for your session by calling `debugBarEnabled()` inside a template.
This feature can be useful if you need to add custom logic for when dot-debugbar is enabled.
