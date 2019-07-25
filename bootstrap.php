<?php

use Strukt\Http\Response;
use Strukt\Http\Request;
use Strukt\Http\RedirectResponse;
use Strukt\Http\JsonResponse;
use Strukt\Http\Session;

use Strukt\Router\Middleware\ExceptionHandler;
use Strukt\Router\Middleware\Authentication; 
use Strukt\Router\Middleware\Authorization;
use Strukt\Router\Middleware\StaticFileFinder;
use Strukt\Router\Middleware\Session as SessionMiddleware;
use Strukt\Router\Middleware\Router as RouterMiddleware;

use Strukt\Provider\Router as RouterProvider;

use Strukt\Env;

$loader = require "vendor/autoload.php";
$loader->add('Blockly', "src/");

Env::set("root_dir", getcwd());
Env::set("rel_static_dir", "/public/static");
Env::set("is_dev", true);

$app = new Strukt\Router\Kernel(Request::createFromGlobals());
$app->inject("app.dep.author", function(){

    return [];

    // return array(

    //     "permissions" => array(

    //         // "show_secrets"
    //     )
    // );
});

$app->inject("app.dep.authentic", function(Session $session){

    $user = new Strukt\User();
    $user->setUsername($session->get("username"));

    return $user;
});

$app->inject("app.dep.session", function(){

    return new Session;
});

$app->providers(array(

    RouterProvider::class
));

$app->middlewares(array(

    ExceptionHandler::class,
    SessionMiddleware::class,
    Authorization::class,
    Authentication::class,
    StaticFileFinder::class,
    RouterMiddleware::class
));

return $app;