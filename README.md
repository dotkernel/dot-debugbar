# dot-debugbar

DotKernel's debug bar built on top of [maximebf/php-debugbar](https://github.com/maximebf/php-debugbar).

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-debugbar)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-debugbar/1.1.4)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-debugbar)](https://github.com/dotkernel/dot-debugbar/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-debugbar)](https://github.com/dotkernel/dot-debugbar/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-debugbar)](https://github.com/dotkernel/dot-debugbar/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-debugbar)](https://github.com/dotkernel/dot-debugbar/blob/1.0/LICENSE.md)

[![Build Static](https://github.com/dotkernel/dot-debugbar/actions/workflows/static-analysis.yml/badge.svg?branch=1.0)](https://github.com/dotkernel/dot-debugbar/actions/workflows/static-analysis.yml)
[![codecov](https://codecov.io/gh/dotkernel/dot-debugbar/graph/badge.svg?token=F0N8VWKTDW)](https://codecov.io/gh/dotkernel/dot-debugbar)

[![SymfonyInsight](https://insight.symfony.com/projects/c1dc83af-a4b3-4a46-a80c-d87dff782089/big.svg)](https://insight.symfony.com/projects/c1dc83af-a4b3-4a46-a80c-d87dff782089)


## Install
Install dot-debugbar in your application by running the following command:

    composer require dotkernel/dot-debugbar


## Setup
Once installed, the following components need to be registered by adding:
* `$app->pipe(\Dot\DebugBar\Middleware\DebugBarMiddleware::class);` to `config/pipeline.php` (preferably after `ServerUrlMiddleware::class`)
* `\Dot\DebugBar\ConfigProvider::class,` to `config/config.php` (preferably at the beginning of the section where the `DotKernel packages` are loaded)
* `\Dot\DebugBar\Extension\DebugBarExtension::class` to `config/autoload/templates.global.php` (inside the array founder under the key `twig` => `extensions`)

Locate the library's assets directory, called `assets` and copy **its contents** to your application under `public/debugbar` directory.

Locate the library's config file `config/debugbar.local.php` and clone it inside your application as:
* `config/autoload/debugbar.local.php.dist`
* `config/autoload/debugbar.local.php`

By default, dot-debugbar is enabled only on the local environment, by whitelisting `127.0.0.1` in the config file, inside the array located under the `ipv4Whitelist` key.
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


## Usage
Other than the data being automatically collected during a session, dot-debugbar can also be used to log messages, measure durations, debug database queries and more...

When you need an instance of DebugBar, locate an instance of it in your application's container using:

    $debugBar = $container->get(\Dot\DebugBar\DebugBar::class);

then your factory can inject `$debugBar` as a dependency in your class.

OR

If you are using [dot-annotated-services](https://github.com/dotkernel/dot-annotated-services) inject it directly in your class's constructor.

Once an instance of DebugBar has been injected in your code, you can access all its features.
The below examples will assume you already have an instance of DebugBar in your code, and it's callable using `$this->debugBar`.


### Logging messages
Results will show up in the debug bar under the `Messages` tab.

Log messages (can be of any type):
```php
$this->debugBar->addMessage(1);
$this->debugBar->addMessage(true);
$this->debugBar->addMessage('foo');
$this->debugBar->addMessage(['foo']);
$this->debugBar->addMessage(new \stdClass());
```

Log messages and set custom label by specifying the 2nd argument (you can use any label, but `error` and `warning` use custom highlight and icons):
```php
$exception = new \Exception('something went wrong');
$this->debugBar->addMessage($exception, 'error');
$this->debugBar->addMessage($exception->getMessage(), 'error');
$this->debugBar->addMessage('some warning', 'warning');
$this->debugBar->addMessage('custom message', 'custom');
```

Also, clicking on a label (found on the bottom right of the debugbar) will toggle the visibility of all messages with that specific label.


### Measure durations
Results will show up in the debug bar under the `Timeline` tab.

In order to measure how long does it take for a piece of code to execute, do the following:
```php
$this->debugBar->measure('long operation', function () {
    // your code here
});
```

OR

```php
$this->debugBar->startTimer('long operation', 'measuring long operation');
// your code here
$this->debugBar->stopTimer('long operation');
```


### Debug Doctrine queries
Results will show up in the debug bar under the `Database` tab.

By default, all queries executed in order to load a page will be logged and displayed under this tab.
If you submit a form that will perform a redirect, you won't see the executed CREATE/UPDATE queries unless you stack the collected data:
```php
$this->debugBar->stackData();
```

The method needs to be called after all database operations have finished AND before emitting the redirect response.
In this case, next to the `Memory usage` widget you'll see a dropdown that allows you to select between the previous page load (with the redirect) and the current one.

### Debug Exceptions
Results will show up in the debug bar under the `Exceptions` tab.

By registering `Dot\DebugBar\Middleware\DebugBarMiddleware`, dot-debugbar is ready to capture Exceptions.
