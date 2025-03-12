# Measure durations

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
