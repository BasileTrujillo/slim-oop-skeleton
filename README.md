# Slim 3 POO Skeleton

[![License](http://poser.pugx.org/l0gin/slim-3-poo-skeleton/license?format=flat)](https://packagist.org/packages/l0gin/slim-3-poo-skeleton)
[![Latest Stable Version](http://poser.pugx.org/l0gin/slim-3-poo-skeleton/v/stable?format=flat)](https://packagist.org/packages/l0gin/slim-3-poo-skeleton)
[![Total Downloads](http://poser.pugx.org/l0gin/slim-3-poo-skeleton/downloads?format=flat)](https://packagist.org/packages/l0gin/slim-3-poo-skeleton)
[![Latest Unstable Version](http://poser.pugx.org/l0gin/slim-3-poo-skeleton/v/unstable?format=flat)](https://packagist.org/packages/l0gin/slim-3-poo-skeleton)

This is a full POO skeleton project for Slim 3 that includes the following usefull dependencies / middlewares :

* [Twig](http://twig.sensiolabs.org/)
* [Slim Flash Messages](https://github.com/slimphp/Slim-Flash) 
* Monolog [Seldaek/monolog](https://github.com/Seldaek/monolog)
* [PDO](http://php.net/manual/fr/book.pdo.php)
* MongoDB [mongo-php-library](http://mongodb.github.io/mongo-php-library) - [Mongo Doc](https://docs.mongodb.com/manual/)
* PHP Debug Bar [maximebf/php-debugbar](https://github.com/maximebf/php-debugbar)
* CLImate [thephpleague/climate](https://github.com/thephpleague/climate)
* Whoops [filp/whoops](https://github.com/filp/whoops)

This skeleton provide:

--> A Web controller (`app/src/Core/Controller/WebController.php`) that you can extend to get default depencies loads.
It load the following services : 

* Twig
* Monolog
* Flash

It also include two Twig templates based on Material Design Lite.
The following Front & Back office templates : 

* Front: https://getmdl.io/templates/android-dot-com/index.html
* Back: https://getmdl.io/templates/dashboard/index.html

--> A secured authenticated API controller using Basic Auth, MongoDB and JSON Web Token (JWT) middlewares.

* GET - `api/auth` to a get a JWT on succesfull authentication.
* GET - `api/user` to retrieves User informations.

--> A CLI controller (`app/src/Core/Controller/CliController.php`) witch load Monolog and CLImate and provide some usefull functions.

A `Setup` Class is provided to help initiate MongoBD structure and creates users.

## Create your project

The project is available on [Packagist](https://packagist.org/packages/l0gin/slim-3-poo-skeleton), so you can create a new project with the following command: 

    $ composer create-project -n -s dev l0gin/slim-3-poo-skeleton my-app

## Run it

1. `$ cd my-app`
2. `$ php -S 0.0.0.0:8888 -t public public/index.php`
3. Browse to http://localhost:8888

Prefer using Apache 2.4 and PHP 7.0 (FPM).

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
* `app/Core/Bootstrap/HttpBootstrap.php`: HTTP Bootstrap class for Web and API endpoints - Load and setup App
* `app/Core/Bootstrap/CliBootstrap.php`: CLI Bootstrap class for CLI endpoints - Load and setup App
* `app/Core/Settings.php`: Default configuration
* `app/Core/Dependencies.php`: Services for Slim DI Container
* `app/Core/Middlewares.php`: Application middlewares
* `app/Core/Routes.php`: All application routes are here
* `app/src/Core/Controller/WebController.php`: Web Controller super class - For Web endpoint using
* `app/src/Core/Controller/ApiController.php`: API Controller super class - For API endpoint using
* `app/src/Core/Controller/CliController.php`: CLI Controller super class - For CLI endpoint using
* `app/src/Controller/Web/Front.php`: Controller class for the home page
* `app/src/Controller/Web/Back.php`: Controller class for the dashboard page
* `app/src/Controller/Api/Auth.php`: Controller class for API Authentication
* `app/src/Controller/Api/User.php`: Controller class for User action through API
* `app/src/Controller/Cli/Setup.php`: Controller class for app setup
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

final class MyController extends WebController
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
        
        // Mongo DB settings        
        'mongo' => [
            'host' => '127.0.0.1',
            'port' => 27017,
            'options' => [
                //"username" => 'foo',
                //"password" => 'bar'
            ],
            'driverOptions' => [],
            'default_db' => 'database'
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
        $this->app->get('/', 'App\Controller\Front:homeAction')
            ->setName('homepage');
            
        $this->app->get('/contact', 'App\Controller\Front:contactAction')
            ->setName('contact');
    }
    
    // /!\ Inside a Routes extended class
    
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

For example, to run `init()` function from `App\CLI\Setup`:

    $ php bin/cli.php -s Setup::init -v
    
Add some verbosity:

    $ php bin/cli.php -s Setup::init -v
    # Or
    $ php bin/cli.php -s Setup::init --verbose
    
Print help:

    $ php bin/cli.php -s Setup::init -h
    # Or
    $ php bin/cli.php -s Setup::init --help
    
Add or update an User by calling `user()` function from `App\Controller\Cli\Setup`:

    $ php bin/cli.php -s Setup::user -l admin2 -p coucou -g test -v
   
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

## Asset Management

To fully deploy project you have some other setup steps to follow:

* Install [NodeJS](https://nodejs.org/)

* Install [Gulp](https://github.com/gulpjs/gulp/blob/master/docs/getting-started.md)

```
    #Install gulp globally:
    $ npm install --global gulp
    
    #Install gulp in your project devDependencies:
    $ npm install --save-dev gulp`
```
    
* Install Gulp dependencies:

    `$ npm i`
    
*  Run Gulp tasks:

```
    # CSS task: clean CSS files
    $ gulp beautify_css
    
    # Deploy assets for production: minify CSS, JS
    $ gulp
    # Or
    $ gulp prod
    
    # Build as you dev (Auto launch 'gulp prod' on file changing)
    $ gulp wath
``` 
    
### Troubleshooting

#### Gulp-autoprefixer issue

If you have an error like that while running *gulp build*

```
#!shell

/var/www/websites/weather-dashboard/web/node_modules/gulp-autoprefixer/node_modules/postcss/lib/lazy-result.js:157
        this.processing = new Promise(function (resolve, reject) {
                              ^
ReferenceError: Promise is not defined
    at LazyResult.async (/var/www/websites/weather-dashboard/web/node_modules/gulp-autoprefixer/node_modules/postcss/lib/lazy-result.js:157:31)
```

Take a look at here: [http://stackoverflow.com/a/32502195](http://stackoverflow.com/a/32502195)

## Twig Extensions

This skeleton provide a custom Twig Extension that implement the following Twig functions:

getCssUrl(asset): 
Return minified css file URL if ['assets']['min'] == true and files exists. 
Otherwise return original css file URL if exists 

```html
    <link rel="stylesheet" href="{{ getCssUrl('front.main.css') }}">
```

getJsUrl(asset): 
Return minified js file URL if ['assets']['min'] == true and files exists. 
Otherwise return original js file URL if exists 

```html
    <script type="text/javascript" src="{{ getJsUrl('front.main.js') }}"></script>
```

### Add Twig Functions

Feel free to add twig functions by editing or overriding `App\Core\Twig\AssetTwigExtension`.
To add a Twig function, just add a function starting with the twig function name and ending with `Function`.

```php
<?php
    /**
     * Twig Function to get JS asset URL
     *
     * @param $filename
     *
     * @return string
     * @throws \Exception
     */
    public function getJsUrlFunction($filename)
    {
        return $this->getAssetUrl('js', $filename);
    }
```

## System Auth

Basic Autentication & JWT:

* https://github.com/tuupola/slim-basic-auth
* https://github.com/tuupola/slim-jwt-auth

Get a JSON Web Token by authenticating in `/api/auth` then just deal with the token.

    $ curl -X GET -H "Authorization: Basic YWRtaW46Y291Y291" -H "Cache-Control: no-cache" "http://www.foo.bar/api/auth"
    
Response:

```json
{
  "status": "ok",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE0Njc4MDU2MDQsImV4cCI6MTQ2NzgxMjgwNCwianRpIjoiR2NRa3VoM3poeVBhRUJyVUVtaGsrdz09Iiwic3ViIjoiNTc3Y2Q1ZWNkNDJhYTQyYzVkMWZmMzQyIn0.4GectmgSi4qOforBGm31Z8Qd4b2kM_EFrNC9TfQXkos"
}
```

Get User informations using the JWT from api auth:

    $ curl -X GET -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE0Njc4MDU2MDQsImV4cCI6MTQ2NzgxMjgwNCwianRpIjoiR2NRa3VoM3poeVBhRUJyVUVtaGsrdz09Iiwic3ViIjoiNTc3Y2Q1ZWNkNDJhYTQyYzVkMWZmMzQyIn0.4GectmgSi4qOforBGm31Z8Qd4b2kM_EFrNC9TfQXkos" -H "Cache-Control: no-cache" "http://www.foo.bar/api/user"

Response:

```json
{
  "login": "admin",
  "created_at": "48482-09-12 15:03:51",
  "updated_at": "48482-09-12 15:03:51",
  "group_name": "test",
  "group_id": "577cd5ebd42aa42c5d1ff341"
}
```

## Roadmap

* Unit test endpoint
* PHP Doc generator
* Rest API Doc generator (Phinx, APIJS, ...)
* Bower to install public vendor assets