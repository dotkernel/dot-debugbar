# dot-debugbar

DotKernel's debug bar built on top of [maximebf/php-debugbar](https://github.com/maximebf/php-debugbar).

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-debugbar)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-debugbar)](https://github.com/dotkernel/dot-debugbar/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-debugbar)](https://github.com/dotkernel/dot-debugbar/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-debugbar)](https://github.com/dotkernel/dot-debugbar/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-debugbar)](https://github.com/dotkernel/dot-debugbar/blob/main/LICENSE.md)

![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-debugbar/1.0.x-dev)


## Install
Install dot-debugbar in your application by running the following command:
```bash
$ composer require dotkernel/dot-debugbar
```


## Setup
Once installed, the following components need to be registered by adding:
* `$app->pipe(\Dot\DebugBar\Middleware\DebugBarMiddleware::class);` to `config/pipeline.php` (preferably after `ServerUrlMiddleware::class`)
* `\Dot\DebugBar\ConfigProvider::class,` to `config/config.php` (preferably at the beginning of the section where the `DotKernel packages` are loaded)
* `\Dot\DebugBar\Extension\DebugBarExtension::class` to `config/autoload/templates.global.php` (inside the array founder under the key `twig` => `extensions`)

Locate the library's assets directory `vendor/maximebf/debugbar/src/DebugBar/Resources` and copy its contents to your application under `public/debugbar` directory.

Locate the library's config file `vendor/dotkernel/dot-debugbar/config/debugbar.local.php` and make a copy of it inside your application as:
* `config/autoload/debugbar.local.php`
* `config/autoload/debugbar.local.php.dist`

By default, the debug bar is enabled only on the local environment, by whitelisting `127.0.0.1` in the config file, inside the array located under the `ipWhitelist` key.
If you need to enable it on other environments as well, just whitelist your public IP address.
It can also be enabled globally, by whitelisting the string `*`.

Inside the config file, you will find additional configurations under the `javascript_renderer` key.
For more configuration values, follow the link in the related comment block.


At this step, the debug bar is not displayed yet. In order to display it, you need to call the following Twig functions from your base layout:
* `{{ debugBarCss()|raw }}` (needs to be placed in the head section of the layout, where the CSS files are included)
* `{{ debugBarJs()|raw }}` (needs to be placed in the footer of the layout, where the JS files are included)

If you plan to enable dot-debugbar on production, make sure you clear the relevant cache items by deleting:
* the config cache file: `data/cache/config-cache.php`
* Twig cache directory: `data/cache/twig`

Additionally, you can check if the debug bar is enabled for your session by calling `debugBarEnabled()` inside a template.
This feature can be useful if you need to add custom logic for when the debug bar is enabled.


## Usage
Other than the data being automatically collected during a session, the debug bar can also be used to log messages, measure durations, debug database queries and more...

When you need an instance of the debug bar, locate an instance of it in your application's container using:
```php
$debugBar = $container->get(\Dot\DebugBar\DebugBar::class);
```
then your factory can inject `$debugBar` as a dependency in your class.

OR

If you are using [dot-annotated-services](https://github.com/dotkernel/dot-annotated-services) inject it directly in your class's constructor.

Once an instance of DebugBar has been injected in your code, you can access all its features.
The below examples will assume you already have an instance of DebugBar in your code, and it's callable using `$this->debugBar`.


### Logging messages
Results will show up in the debug bar under the `Messages` tab.

Log messages (can be of any type):
```php
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

Also, clicking on a label will toggle the visibility of all messages with that specific label.


### Measure durations
Results will show up in the debug bar under the `Timeline` tab.

In order to measure how long does it take for a piece of code to execute, do the following:
```php
$this->debugBar->startTimer('long operation', 'measuring long operation');
// do stuff that needs to be measured
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
