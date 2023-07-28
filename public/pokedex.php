<?php
use Slim\Views\Twig;

require_once __DIR__ . '/../util/dbconnect.php';
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
            GROUP_CONCAT( pt.name_en ) AS types_name_en,
            GROUP_CONCAT( pt.name_zh_hk ) AS types_name_zh_hk,
            GROUP_CONCAT( pt.name_zh_cn ) AS types_name_zh_cn,
            GROUP_CONCAT( pt.name_ja ) AS types_name_ja,
            GROUP_CONCAT( pt.color ) AS types_color,
            GROUP_CONCAT( psf.name_en ) AS special_forms_name_en,
            GROUP_CONCAT( psf.name_ja ) AS special_forms_name_ja,
            GROUP_CONCAT( psf.name_zh_cn ) AS special_forms_name_zh_cn,
            GROUP_CONCAT( psf.name_zh_hk ) AS special_forms_name_zh_hk 
        FROM
            pokemon AS pm
            LEFT JOIN (
            SELECT
                ptr1.pokemon_id,
                GROUP_CONCAT( ptr1.type_id ) AS type_id,
                GROUP_CONCAT( pt1.name_en ) AS name_en,
                GROUP_CONCAT( pt1.name_ja ) AS name_ja,
                GROUP_CONCAT( pt1.name_zh_cn ) AS name_zh_cn,
                GROUP_CONCAT( pt1.name_zh_hk ) AS name_zh_hk,
                GROUP_CONCAT( pt1.color ) AS color 
            FROM
                pokemon_type_relation AS ptr1
                LEFT JOIN pokemon_type AS pt1 ON ptr1.type_id = pt1.id 
            GROUP BY
                ptr1.pokemon_id 
            ) AS pt ON pm.id = pt.pokemon_id
            LEFT JOIN (
            SELECT
                psfr1.pokemon_id,
                GROUP_CONCAT( psfr1.special_form_id ) AS special_form_id,
                GROUP_CONCAT( psf1.name_en ) AS name_en,
                GROUP_CONCAT( psf1.name_ja ) AS name_ja,
                GROUP_CONCAT( psf1.name_zh_cn ) AS name_zh_cn,
                GROUP_CONCAT( psf1.name_zh_hk ) AS name_zh_hk 
            FROM
                pokemon_special_form_relation AS psfr1
                LEFT JOIN pokemon_special_form AS psf1 ON psfr1.special_form_id = psf1.id 
            GROUP BY
                psfr1.pokemon_id 
            ) AS psf ON psf.pokemon_id = pm.id 
        -- WHERE
        --     pm.id >= 1913 
        --     AND pm.id <= 1916 
        GROUP BY
            pm.id 
        ORDER BY
            pm.zukan_id,
            pm.zukan_sub_id,
            pm.id 
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