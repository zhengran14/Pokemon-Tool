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
    $sql = <<<SQL
        SELECT
            pm.*,
            COUNT(pt.id) AS types_count,
            GROUP_CONCAT( pt.name_en ) AS types_name_en,
            GROUP_CONCAT( pt.name_zh_hk ) AS types_name_zh_hk,
            GROUP_CONCAT( pt.name_zh_cn ) AS types_name_zh_cn,
            GROUP_CONCAT( pt.name_ja ) AS types_name_ja,
            GROUP_CONCAT( pt.color ) AS types_color
        FROM
            pokemon AS pm
            LEFT JOIN pokemon_type_relation AS ptr ON pm.id = ptr.pokemon_id
            LEFT JOIN pokemon_type AS pt ON ptr.type_id = pt.id 
        GROUP BY
            pm.id
        ORDER BY
            pm.zukan_id, pm.zukan_sub_id, pm.id
        LIMIT ?, ?
        SQL;
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ii', $from, $perPage);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($_pokemon = $result->fetch_object('Pokemon')) {
        $pokemon = new Pokemon();
        $pokemon->fromSQL($_pokemon);
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