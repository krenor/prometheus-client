Tracking execution time
===========

## Introduction
The `TracksExecutionTime` trait offers the possibility to track execution time of code.  
Only the [Counters](COUNTERS.md) don't implement this trait.


## Methods

#### `chronometer(array $labels = [], int $precision = 4)`

Returns a closure which allows tracking of execution time. Any time spent between calling  
the `chronometer()` function and its returning closure call is tracked. The closure itself allows setting  
additional labels if these can't be defined before the tracked code is executed (e.g. response time).

## Example

```php
<?php

// Assuming this histogram contains the label names "foo" and "bar".
// Set the label value of "foo" to "baz" as it can be determined beforehand.
$track = $histogram->chronometer(['baz']);

// Execute some code which determines the label value to be used for "bar".

$track([$value]);
```
