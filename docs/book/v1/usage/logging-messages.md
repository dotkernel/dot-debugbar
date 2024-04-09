# Logging messages

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
