Exceptions
==========

## PrometheusException

Thrown when prometheus specific errors happen, e.g. [a metric name is invalid](metrics/README.md#name).  
Extends [InvalidArgumentException](http://php.net/manual/en/class.invalidargumentexception.php).

## LabelException

Dedicated exception regarding metric labels errors.  
Extends [PrometheusException](#prometheusexception).

## StorageException

Thrown when a storage encounters an error.  
Extends [RuntimeException](http://php.net/manual/en/class.runtimeexception.php).

## SamplesCollectorException

Thrown when an error during samples collection occurs.
