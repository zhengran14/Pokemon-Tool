<?php
use Slim\Views\Twig;

require_once __DIR__ . '/../util/dbconnect.php';

$app->get('/ribbon', function ($request, $response, $args) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'ribbon.html', [
        'title' => '奖章/证章'
    ]);
});