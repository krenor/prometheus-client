Text Renderer
=============

## Introduction

Since Prometheus 2.0 removed support for the protocol-buffer format and only supports  
the text-based format the first one is not planned to be implemented by this library.

You can read the [official documentation][text-format] for further details regarding the text format.

## Example

```php
<?php

use Krenor\Prometheus\CollectorRegistry;
use Krenor\Prometheus\Renderer\TextRenderer;

$registry = new CollectorRegistry;

// Fiddle with metrics..

$samples = $registry->collect();
$metrics = (new TextRenderer)->render($samples);
```

[text-format]: https://prometheus.io/docs/instrumenting/exposition_formats/#text-format-details
