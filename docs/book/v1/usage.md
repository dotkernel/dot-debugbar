# Usage

Other than the data being automatically collected during a session, dot-debugbar can also be used to log messages, measure durations, debug database queries and more...

When you need an instance of DebugBar, locate an instance of it in your application's container using:

```php
$debugBar = $container->get(\Dot\DebugBar\DebugBar::class);
```

then your factory can inject `$debugBar` as a dependency in your class.

OR

If you are using [dot-annotated-services](https://github.com/dotkernel/dot-annotated-services) inject it directly in your class's constructor.

Once an instance of DebugBar has been injected in your code, you can access all its features.
The below examples will assume you already have an instance of DebugBar in your code, and it's callable using `$this->debugBar`:

* [Logging messages](usage/logging-messages.md)
* [Measure durations](usage/measure-durations.md)
* [Debug Doctrine queries](usage/debug-doctrine-queries.md)
* [Debug Exceptions](usage/debug-exceptions.md)
