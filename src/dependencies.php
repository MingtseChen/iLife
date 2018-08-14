<?php
// DIC configuration

$container = $app->getContainer();

// view controller
//$container['controller'] = function ($app) {
//    $controller = new App\Controller\RootController($app);
//    return $controller;
//};

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Twig
$container['twig_profile'] = function () {
    return new Twig_Profiler_Profile();
};

$container['view'] = function ($c) {
    $settings = $c->get('settings')['view'];
    $view = new \Slim\Views\Twig($settings['template_path'], $settings['twig']);
    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Knlv\Slim\Views\TwigMessages(new Slim\Flash\Messages()));
    $view->addExtension(new Twig_Extension_Profiler($c['twig_profile']));
    $view->addExtension(new Twig_Extension_Debug());
    //add global for twig(can access php vars)
    $view->getEnvironment()->addGlobal('_session', $_SESSION);
    $view->getEnvironment()->addGlobal('_post', $_POST);
    $view->getEnvironment()->addGlobal('_get', $_GET);
    return $view;
};

//slim flash
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

// database
ORM::configure($container->get('settings')['db']);

//session
$container['session'] = function ($c) {
    return new \SlimSession\Helper;
};

//Storage setting
$container['repair_storage'] = function ($c) {
    $settings = $c->get('settings')['storage'];
    return $settings['repair_path'];
};
