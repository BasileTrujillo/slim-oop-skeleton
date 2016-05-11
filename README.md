# Slim 3 Skeleton

This is a skeleton project for Slim 3 that includes Twig, Flash messages and Monolog.
It was created with akrabat/slim3-skeleton (https://github.com/akrabat/slim3-skeleton)

And include a Front & Back office based on Material Design Lite templates : 
* Front: https://getmdl.io/templates/android-dot-com/index.html
* Back: https://getmdl.io/templates/dashboard/index.html

## Create your project:

    $ composer create-project -n -s dev akrabat/slim3-skeleton my-app

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
* `vendor`: Composer dependencies

## Key files

* `public/index.php`: Entry point to application
* `app/settings.php`: Configuration
* `app/dependencies.php`: Services for Pimple
* `app/middleware.php`: Application middleware
* `app/routes.php`: All application routes are here
* `app/src/Action/HomeAction.php`: Action class for the home page
* `app/templates/home.twig`: Twig template file for the home page
