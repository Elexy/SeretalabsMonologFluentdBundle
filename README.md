# LogglyBundle

    Bundle is not maintained anymore, but mostly works because of its simplicity.

[Loggly](http://loggly.com/) handler for [Monolog](https://github.com/Seldaek/monolog) as a Symfony bundle.

The bundle is inspired from [Monologgly](https://github.com/pradador/Monologgly)

## Installation

Deps

    [WhitewashingLogglyBundle]
        git=https://github.com/beberlei/WhitewashingLogglyBundle.git
        target=/bundles/Whitewashing/Bundle/LogglyBundle

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
