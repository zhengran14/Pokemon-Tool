<?php
use Slim\Views\Twig;

require __DIR__ . '/../util/dbconnect.php';
require __DIR__ . '/../model/pokemon.php';

$app->get('/pokedex', function ($request, $response, $args) {
    $view = Twig::fromRequest($request);
    return $view->render($response, 'pokedex.html', [
        'title' => 'Pokédex'
    ]);
});

$app->get('/pokedex/list', function ($request, $response, $args) {
    $perPage = 30;
    $from = (intval($request->getQueryParams()['page'] ?? -1) - 1) * $perPage;
    $mysqli = openDB();

    // Get one page
    $pokemons = array();
    $stmt = $mysqli->prepare('SELECT * FROM pokemon ORDER BY zukan_id, zukan_sub_id, id LIMIT ?, ?');
    $stmt->bind_param('ii', $from, $perPage);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($pokemon = $result->fetch_object('Pokemon')) {
        array_push($pokemons, $pokemon);
    }
    $result->free();
    $stmt->free_result();
    $stmt->close();

    // Get total pages
    $stmt = $mysqli->prepare('SELECT COUNT(*) FROM pokemon');
    $stmt->execute();
    $stmt->bind_result($totalPages);
    $stmt->fetch();
    $stmt->free_result();
    $stmt->close();

    $mysqli->close();


    $result = array();
    $result['pokemons'] = $pokemons;
    $result['totalPages'] = ceil($totalPages / $perPage);
    $result['perPage'] = $perPage;
    $response->getBody()->write(json_encode($result));
    return $response;
});