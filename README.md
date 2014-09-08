# SeretalabsMonologFluentdBundle

    This bundle enables logging to the fluentd log concentrator from Symfony.
    Fluentd is an open source datacollector and decouples your logging collection
    and analysis tools from your project by sending all logs from deamons, apps
    in front and backend to one single daemon and use filtering to selectively
    forward to storage engine / analysis tools

[fluentd](http://www.fluentd.org/) handler for [Monolog](https://github.com/Seldaek/monolog) as a Symfony bundle.

## Installation

Kernel

    $bundles = array(
        //..
        new Seretalabs\Bundle\MonologFluentdBundle\SeretalabsMonologFluentdBundle(),
    );

Autoload:

    $loader->registerNamespaces(array(
        //..
        'Seretalabs' => __DIR__.'/../vendor/bundles',
    ));

## Configuration

Configure Monolog

    monolog:
        handlers:
            main:
                type:         fingers_crossed
                action_level: error
                handler:      fluentd
            fluentd:
                type: service
                id: monolog_fluentd.monolog_handler

Configure monolog for fluentd:

    monolog_fluentd:
        # fluentd API host
        host: localhost

        # fluentd API port default 24224
        port: 24224

        # Level to be logged (defaults to DEBUG)
        level: DEBUG

        bubble: true

## Usage

```php
<?php
use Monolog\Logger;

$logger->debug('example.monolog', array('foo' => 'bar'));
$logger->info('example.fluentd', array('fizz' => 'buzz'));

// Fluentd:
// 2013-10-11 01:00:00 +0900 dakatsuka.example.monolog: {"foo":"bar","level":"DEBUG"}
// 2013-10-11 01:00:00 +0900 dakatsuka.example.fluentd: {"fizz":"buzz","level":"INFO"}
```

## credits
	This bundle uses [daktsuka](https://github.com/dakatsuka)'s fluentd handler for Monolog [https://github.com/dakatsuka/MonologFluentHandler]