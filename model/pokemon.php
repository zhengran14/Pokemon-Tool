<?php

require __DIR__ . '/../model/pokemon_type.php';

class Pokemon {
    public $id;
    public $zukan_id;
    public $zukan_sub_id;
    public $name_zh_cn;
    public $name_zh_hk;
    public $name_ja;
    public $name_en;
    public $weight;
    public $height;
    public $img_file;
    public $wiki_url_zh_cn;
    public $pokemonTypes;

    public function fromSQL($_pokemon) {
        $this->id = $_pokemon->id;
        $this->zukan_id = $_pokemon->zukan_id;
        $this->zukan_sub_id = $_pokemon->zukan_sub_id;
        $this->name_zh_cn = $_pokemon->name_zh_cn;
        $this->name_zh_hk = $_pokemon->name_zh_hk;
        $this->name_ja = $_pokemon->name_ja;
        $this->name_en = $_pokemon->name_en;
        $this->weight = $_pokemon->weight;
        $this->height = $_pokemon->height;
        $this->img_file = $_pokemon->img_file;
        $this->wiki_url_zh_cn = $_pokemon->wiki_url_zh_cn;

        $this->pokemonTypes = array();
        if (isset($_pokemon->types_count)) {
            $types_name_en = explode(',', $_pokemon->types_name_en);
            $types_name_zh_hk = explode(',', $_pokemon->types_name_zh_hk);
            $types_name_zh_cn = explode(',', $_pokemon->types_name_zh_cn);
            $types_name_ja = explode(',', $_pokemon->types_name_ja);
            $types_color = explode(',', $_pokemon->types_color);

            for ($i = 0; $i < $_pokemon->types_count; $i++) {
                $pokemonType = new PokemonType();
                $pokemonType->name_en = $types_name_en[$i];
                $pokemonType->name_zh_hk = $types_name_zh_hk[$i];
                $pokemonType->name_zh_cn = $types_name_zh_cn[$i];
                $pokemonType->name_ja = $types_name_ja[$i];
                $pokemonType->color = $types_color[$i];
                array_push($this->pokemonTypes, $pokemonType);
            }
        }
    }
}