<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addErrorMiddleware(true, false, false);
// $app->addErrorMiddleware(false, true, true);
$twig = Twig::create('../view', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

require __DIR__ . '/pokedex.php';

$app->run();