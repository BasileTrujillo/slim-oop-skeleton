<?php
// Application middleware

if ($container->get('settings')['debugbar']['enabled'] === true) {
    $app->add(new PhpMiddleware\PhpDebugBar\PhpDebugBarMiddleware(
        $container->get('debugbar')->getJavascriptRenderer('/phpdebugbar')
    ));
}
