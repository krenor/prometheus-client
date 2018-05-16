TextRenderer
============

## Introduction

Since Prometheus 2.0 removed support for the protocol-buffer format and only supports  
the text-based format the first one is not planned to be offered by this library.

You can read the [prometheus docs][text-format] for further details regarding the text format.

## Example

```php
<?php

use Krenor\Prometheus\CollectorRegistry;
use Krenor\Prometheus\Renderer\TextRenderer;

$registry = new CollectorRegistry;

// do some metrics stuff..

$samples = $registry->collect();
$metrics = (new TextRenderer)->render($samples);
```

[text-format]: https://prometheus.io/docs/instrumenting/exposition_formats/#text-format-details
