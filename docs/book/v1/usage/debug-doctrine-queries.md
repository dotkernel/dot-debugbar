# Debug Doctrine queries

Results will show up in the debug bar under the `Database` tab.

By default, all queries executed in order to load a page will be logged and displayed under this tab.
If you submit a form that will perform a redirect, you won't see the executed CREATE/UPDATE queries unless you stack the collected data:

```php
$this->debugBar->stackData();
```

The method needs to be called after all database operations have finished AND before emitting the redirect response.
In this case, next to the `Memory usage` widget you'll see a dropdown that allows you to select between the previous page load (with the redirect) and the current one.
