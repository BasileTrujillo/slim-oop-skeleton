# Slim 3 Skeleton

This is a skeleton project for Slim 3 that includes the following usefull dependencies :

* Twig
* Slim Flash Messages
* Monolog
* PDO

This skeleton provide a base controller (`app/src/core/BaseController.php`) that you can extend to get default depencies loads.

It also include two Twig template based on Material Design Lite.
The following Front & Back office templates : 
* Front: https://getmdl.io/templates/android-dot-com/index.html
* Back: https://getmdl.io/templates/dashboard/index.html

## Create your project:

    $ composer create-project -n -s dev l0gin/slim-3-mdl-skeleton my-app

### Run it:

1. `$ cd my-app`
2. `$ php -S 0.0.0.0:8888 -t public public/index.php`
3. Browse to http://localhost:8888

## Key directories

* `app`: Application code
* `app/src`: All class files within the `App` namespace
* `app/templates`: Twig template files
* `cache/twig`: Twig's Autocreated cache files
* `log`: Log files
* `public`: Webserver root
* `public/assets`: Public ressources (css, js, img, ...)
* `vendor`: Composer dependencies

## Key files

* `public/index.php`: Entry point to application
* `app/settings.php`: Configuration
* `app/dependencies.php`: Services for Pimple
* `app/middleware.php`: Application middleware
* `app/routes.php`: All application routes are here
* `app/src/controller/FrontController.php`: Controller class for the home page
* `app/src/controller/BackController.php`: Controller class for the dashboard page
* `app/src/core/BaseController.php`: Controller super class
* `app/templates/layouts/front.twig`: Main Twig template file for front layout pages
* `app/templates/layouts/back.twig`: Main Twig template file for back layout pages
* `app/templates/pages/front/*`: Twig template files for front pages
* `app/templates/pages/back/*`: Twig template files for back pages

## Use dependencies

In your controllers override the parrent contructor and load your dependency from Slim Container.

### Load PDO dependency

```php
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

## Override / Set local (environement) settings

Copy `local.settings.php.dist` to `lcal.settings.php` and add your env settings.

```php
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

