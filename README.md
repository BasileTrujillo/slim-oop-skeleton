# Slim 3 Skeleton

This is a full POO skeleton project for Slim 3 that includes the following usefull dependencies :

* [Twig](http://twig.sensiolabs.org/)
* [Slim Flash Messages](https://github.com/slimphp/Slim-Flash) 
* Monolog [Seldaek/monolog](https://github.com/Seldaek/monolog)
* [PDO](http://php.net/manual/fr/book.pdo.php)
* PHP Debug Bar [maximebf/php-debugbar](https://github.com/maximebf/php-debugbar)
* CLImate [thephpleague/climate](https://github.com/thephpleague/climate)

This skeleton provide:

A base controller (`app/src/core/BaseController.php`) that you can extend to get default depencies loads
It load the following services : 

* Twig
* Monolog
* Flash

And add settings array to view rendered.

A base CLI controller (`app/src/core/BaseCliController.php`) witch load Monolog and CLImate and provide some usefull functions

It also include two Twig templates based on Material Design Lite.
The following Front & Back office templates : 

* Front: https://getmdl.io/templates/android-dot-com/index.html
* Back: https://getmdl.io/templates/dashboard/index.html

## Create your project

    $ composer create-project -n -s dev l0gin/slim-3-mdl-skeleton my-app

## Run it

1. `$ cd my-app`
2. `$ php -S 0.0.0.0:8888 -t public public/index.php`
3. Browse to http://localhost:8888

Prefer using Apache 2.4 and PHP 7.0 (FPM).

## Demo

* Front: http://slim-3-mdl-skeleton.l0gin.fr/
* Back: http://slim-3-mdl-skeleton.l0gin.fr/admin

## Key directories

* `app`: Application code
* `app/src`: All class files within the `App` namespace
* `app/templates`: Twig template files
* `app/conf`: Custom / environment settings
* `cache/twig`: Twig's Autocreated cache files
* `log`: Log files
* `public`: Webserver root
* `public/assets`: Public ressources (css, js, img, ...)
* `vendor`: Composer dependencies

## Key files

* `public/index.php`: Entry point to application
* `app/Core/Bootstrap.php`: Bootstrap class / Load and setup App
* `app/Core/Settings.php`: Default configuration
* `app/Core/Dependencies.php`: Services for Slim DI Container
* `app/Core/Middlewares.php`: Application middlewares
* `app/Core/Routes.php`: All application routes are here
* `app/src/Core/BaseController.php`: Controller super class
* `app/src/Controller/FrontController.php`: Controller class for the home page
* `app/src/Controller/BackController.php`: Controller class for the dashboard page
* `app/conf/local.settings.php.dist`: Copy this file as local.settings.php and add your custom / environment settings
* `app/templates/layouts/front.twig`: Main Twig template file for front layout pages
* `app/templates/layouts/back.twig`: Main Twig template file for back layout pages
* `app/templates/pages/front/*`: Twig template files for front pages
* `app/templates/pages/back/*`: Twig template files for back pages

## Use dependencies

In your controllers override the parent contructor and load your dependency from Slim Container.

### Load PDO dependency

```php
<?php

final class MyController extends BaseController
{
    /**
     * @var \PDO PDO Instance
     */
    private $pdo;

    /**
     * MyController constructor override
     *
     * @param Container $c Slim App Container
     */
    public function __construct(Container $c)
    {
        $this->pdo = $c->get('pdo');

        parent::__construct($c);
    }
 
    //...   
}
```

## Override / Set local (environment) settings

Copy `local.settings.php.dist` to `local.settings.php` and add your env settings.

```php
<?php

return [
    'settings' => [

        //Slim Settings
        'displayErrorDetails' => true, //Display Slim Errors

        // PDO settings
        'pdo' => [
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => 'mydb',
            'user'      => 'foo',
            'passwd'    => 'bar'
        ],

        //Google Analytics
        'google_analytics' => [
            'api_key' => 'UA-XXXXX-Y',
            'anonymize_ip' => false
        ]
    ]
];
```

## Adding Dependencies / Middlewares / Routes

`App\Core\Dependencies`, `App\Core\Middlewares` and `App\Core\Routes` classes have 
an auto-load function witch call every methods starting with `load` (such as `loadTwig`).

So, to add a service, a middleware or a new route function (multiple routes can be added into each `load` function),
add a function like the following services setup : 

```php
<?php

/**
 * Load MongoDB
 */
protected function loadMongoDB()
{
    /**
     * MongoDB Client
     *
     * @param Container $c
     *
     * @return \MongoDB\Client
     */
    $this->dic['mongo'] = function (Container $c) {
        $settings = $c->get('settings')['mongo']; //@TODO: Add this setting into Settings.php and/or local.settings.php
        $client = new \MongoDB\Client('mongodb://' . $settings['host'] . ':' . $settings['port']);
        return $client;
    };
}

/**
 * Load Faker
 */
protected function loadFaker()
{
    /**
     * Faker Factory Service
     *
     * @param Container $c
     *
     * @return \Faker\Factory
     */
    $this->dic['faker'] = function (Container $c) {
        return Faker\Factory::create();
    };
}

/**
 * Load AMQP Stream Connection
 */
protected function loadAMQP()
{
    /**
     * AMQP Stream Connection
     *
     * @param Container $c
     *
     * @return \PhpAmqpLib\Connection\AMQPStreamConnection
     */
    $this->dic['amqp'] = function (Container $c) {
        $settings = $c->get('settings')['amqp']; //@TODO: Add this setting into Settings.php and/or local.settings.php
        return new \PhpAmqpLib\Connection\AMQPStreamConnection(
            $settings['amqp']['host'],
            $settings['amqp']['port'],
            $settings['amqp']['user'],
            $settings['amqp']['pass']
        );
    };
}
```

To simply add a `front-office` route, just add it into `loadFrontRoutes()` function.
(Directly inside `Routes` class or inside an extended class).

```php
<?php

    // /!\ Directly inside Routes class
    
    /**
     * Load front-office routes
     */
    protected function loadFrontRoutes()
    {
        $this->app->get('/', 'App\Controller\FrontController:homeAction')
            ->setName('homepage');
            
        $this->app->get('/contact', 'App\Controller\FrontController:contactAction')
            ->setName('contact');
    }
    
    // /!\ Inside an Routes extended class
    
    class MyRoutes extends \App\Core\Routes
    {
        //...
        
        /**
         * Load front-office routes
         */
        protected function loadFrontRoutes()
        {
            parent::loadFrontRoutes();
                
            $this->app->get('/contact', 'App\Controller\FrontController:contactAction')
                ->setName('contact');
        }
        
        //...
    }
```

## Debug Bar

`Displays a debug bar in the browser with information from php. No more var_dump() in your code!`

https://github.com/maximebf/php-debugbar

`maximebf/php-debugbar` is handled with `php-middleware/phpdebugbar` from https://github.com/php-middleware/phpdebugbar

It load the following collectors: 

Default collectors: 

* PhpInfoCollector
* MessagesCollector
* RequestDataCollector
* TimeDataCollector
* MemoryCollector
* ExceptionsCollector

Extra collectors:

* ConfigCollector (based on app settings)
* MonologCollector
* PDOCollector

## Command Line Interface (PHP CLI) Endpoint

This skeleton provide a CLI endpoint to help you create cli script using core app dependency injection and all other stuff.

For example, to run init() function from `App\CLI\Setup`:

    $ php bin/cli.php -s Setup::init

Add some verbosity:

    $ php bin/cli.php -s Setup::init -v
    # Or
    $ php bin/cli.php -s Setup::init --verbose
    
Print help:

    $ php bin/cli.php -s Setup::init -h
    # Or
    $ php bin/cli.php -s Setup::init --help
   
### Add argument and help

To add argument check, override checkParameters() function like the `Setup.php` does 
and use `addParameter()` function to:

* Add short and long option (related to [getOpt()](http://php.net/manual/fr/function.getopt.php))
* Describe argument
* Add example(s)

Description and example(s) are automaticaly printed by `printHelp()` function.

```php
<?php
    /**
     * Custom parameter check
     *
     * @return bool Return false will automaticaly call printHelp() function and stop script execution
     */
    public function checkParameters()
    {
        // Add custom parameter
        $this->addParameter('a', 'all', 'Setup All', '--all');

        if(parent::checkParameters()) {
        
            // Check custom parameter
            $aOpt = $this->getArg('a');
            if ($aOpt !== null) {
                $this->initAll = true;
            }
            
            return true;
        } else {
            return false;
        }
    }
```
